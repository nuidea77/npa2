<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\Stamp;
use App\Models\StampLog;
use App\Models\StampYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StampsController extends Controller
{
    /** Нийт тамга цуглуулсан ХЗ-дын тойм + тамгануудын жагсаалт */
    public function index(Request $request)
    {
        $q = Stamp::with(['org:id,name', 'user:id,name,email,position,photo'])->orderByDesc('stamp_date');

        if ($request->filled('org_id')) {
            $q->where('org_id', $request->query('org_id'));
        }
        if ($request->filled('year')) {
            $q->where('year', (int) $request->query('year'));
        }
        if (!$request->boolean('with_deleted')) {
            $q->where('status', '!=', 'deleted');
        }

        $summary = Org::withCount(['stamps as stamps_count' => fn ($w) => $w->where('status', '!=', 'deleted')])
            ->orderByDesc('stamps_count')->get(['id', 'name']);

        return response()->json([
            'stamps' => $q->get(),
            'summary' => $summary,
            'years' => StampYear::orderByDesc('year')->get(),
        ]);
    }

    /** ХЗ-д тамга нэмэх */
    public function store(Request $request)
    {
        $request->merge(['user_id' => $request->input('user_id') ?: null]);

        $data = $request->validate([
            'org_id' => ['required', 'exists:orgs,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:300'],
            'staff_name' => ['nullable', 'string', 'max:190'],
            'staff_position' => ['nullable', 'string', 'max:190'],
            'stamp_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $data = $this->applySelectedUser($data, (int) $data['org_id']);

        $year = (int) date('Y', strtotime($data['stamp_date']));

        // Жилд хамгийн ихдээ 50 тамга цуглуулах боломжтой
        $count = Stamp::where('org_id', $data['org_id'])->where('year', $year)
            ->where('status', '!=', 'deleted')->count();
        if ($count >= 50) {
            return response()->json(['message' => 'Энэ ХЗ тухайн жилдээ 50 тамганы дээд хязгаартаа хүрсэн байна.'], 422);
        }

        $stamp = Stamp::create([...$data, 'year' => $year, 'status' => 'active']);

        StampLog::create([
            'stamp_id' => $stamp->id,
            'action' => 'created',
            'admin_name' => $request->user()->email,
            'note' => 'Тамга нэмэгдэв',
        ]);

        return response()->json(['message' => 'Тамга нэмэгдлээ.', 'stamp' => $stamp->load(['org:id,name', 'user:id,name,email,position,photo'])], 201);
    }

    /** Тамга засварлах */
    public function update(Request $request, int $id)
    {
        $stamp = Stamp::findOrFail($id);

        if ($request->has('user_id')) {
            $request->merge(['user_id' => $request->input('user_id') ?: null]);
        }

        $data = $request->validate([
            'org_id' => ['sometimes', 'exists:orgs,id'],
            'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:300'],
            'staff_name' => ['sometimes', 'nullable', 'string', 'max:190'],
            'staff_position' => ['sometimes', 'nullable', 'string', 'max:190'],
            'stamp_date' => ['sometimes', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        $data = $this->applySelectedUser($data, (int) ($data['org_id'] ?? $stamp->org_id));

        $stamp->fill($data);
        if (isset($data['stamp_date'])) {
            $stamp->year = (int) date('Y', strtotime($data['stamp_date']));
        }
        if ($stamp->status === 'active') {
            $stamp->status = 'edited';
        }
        $stamp->save();

        StampLog::create([
            'stamp_id' => $stamp->id,
            'action' => 'edited',
            'admin_name' => $request->user()->email,
            'note' => $request->input('log_note', 'Мэдээлэл засварлав'),
        ]);

        return response()->json(['message' => 'Засварлагдлаа.', 'stamp' => $stamp->load(['org:id,name', 'user:id,name,email,position,photo'])]);
    }

    /**
     * Сонгосон хэрэглэгч тухайн ХЗ-нд харьяалагдаж буйг шалгаад,
     * ажилтны нэр/албан тушаалыг тухайн үеийн байдлаар хуулж авна.
     */
    private function applySelectedUser(array $data, int $orgId): array
    {
        if (empty($data['user_id'])) {
            return $data;
        }

        $user = User::find($data['user_id']);

        if (!$user || (int) $user->org_id !== $orgId) {
            throw ValidationException::withMessages([
                'user_id' => 'Сонгосон хэрэглэгч тухайн Хамгаалалтын захиргаанд харьяалагдахгүй байна.',
            ]);
        }

        // Гараар өөр нэр бичсэн бол түүнийг нь хүндэтгэнэ, эс бөгөөс хэрэглэгчээс авна
        $data['staff_name'] = trim((string) ($data['staff_name'] ?? '')) ?: $user->name;
        $data['staff_position'] = trim((string) ($data['staff_position'] ?? '')) ?: $user->position;

        return $data;
    }

    /** Тамга хасах (шалтгаантай, лог үлдэнэ, тоо автоматаар шинэчлэгдэнэ) */
    public function destroy(Request $request, int $id)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ], ['reason.required' => 'Хасах шалтгаанаа бичнэ үү.']);

        $stamp = Stamp::findOrFail($id);
        $stamp->status = 'deleted';
        $stamp->delete_reason = $data['reason'];
        $stamp->deleted_at = now();
        $stamp->save();

        StampLog::create([
            'stamp_id' => $stamp->id,
            'action' => 'deleted',
            'admin_name' => $request->user()->email,
            'note' => $data['reason'],
        ]);

        return response()->json(['message' => 'Тамга хасагдлаа.']);
    }

    /** Тамганы дэлгэрэнгүй + админы лог */
    public function show(int $id)
    {
        return response()->json(['stamp' => Stamp::with(['org:id,name', 'user:id,name,email,position,photo', 'logs'])->findOrFail($id)]);
    }

    /** Жил бүрийн тамганы тохиргоо (эхлэх, дуусах огноо, загвар зураг) */
    public function yearsSave(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2012', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'design_image_url' => ['nullable', 'string', 'max:500'],
            'design_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ], [
            'design_image.image' => 'Зөвхөн зураг файл байршуулна уу.',
            'design_image.mimes' => 'Зөвхөн PNG, JPG, WEBP өргөтгөлтэй зураг байршуулна уу.',
            'design_image.max' => 'Зургийн хэмжээ 4MB-аас хэтрэхгүй байх ёстой.',
            'end_date.after' => 'Дуусах огноо нь эхлэх огнооноос хойш байх ёстой.',
        ]);

        $year = StampYear::firstOrNew(['year' => $data['year']]);
        $year->start_date = $data['start_date'];
        $year->end_date = $data['end_date'];

        $previous = $year->design_image;

        if ($request->hasFile('design_image')) {
            $year->design_image = '/storage/' . $request->file('design_image')->store('uploads/stamps', 'public');
        } else {
            // Хоосон утга ирвэл зургийг авч хаяна
            $year->design_image = ($data['design_image_url'] ?? null) ?: null;
        }

        $year->save();

        // Солигдсон бол өмнө нь байршуулсан файлыг цэвэрлэнэ (гараар тавьсан замд хүрэхгүй)
        if ($previous && $previous !== $year->design_image && str_starts_with($previous, '/storage/uploads/stamps/')) {
            Storage::disk('public')->delete(substr($previous, strlen('/storage/')));
        }

        return response()->json(['message' => 'Хадгалагдлаа.', 'year' => $year]);
    }

    public function yearsDelete(int $id)
    {
        StampYear::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }
}
