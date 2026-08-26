<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\Stamp;
use App\Models\StampLog;
use App\Models\StampYear;
use Illuminate\Http\Request;

class StampsController extends Controller
{
    /** Нийт тамга цуглуулсан ХЗ-дын тойм + тамгануудын жагсаалт */
    public function index(Request $request)
    {
        $q = Stamp::with('org:id,name')->orderByDesc('stamp_date');

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
        $data = $request->validate([
            'org_id' => ['required', 'exists:orgs,id'],
            'name' => ['required', 'string', 'max:300'],
            'staff_name' => ['nullable', 'string', 'max:190'],
            'staff_position' => ['nullable', 'string', 'max:190'],
            'stamp_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

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

        return response()->json(['message' => 'Тамга нэмэгдлээ.', 'stamp' => $stamp->load('org:id,name')], 201);
    }

    /** Тамга засварлах */
    public function update(Request $request, int $id)
    {
        $stamp = Stamp::findOrFail($id);

        $data = $request->validate([
            'org_id' => ['sometimes', 'exists:orgs,id'],
            'name' => ['sometimes', 'string', 'max:300'],
            'staff_name' => ['sometimes', 'nullable', 'string', 'max:190'],
            'staff_position' => ['sometimes', 'nullable', 'string', 'max:190'],
            'stamp_date' => ['sometimes', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

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

        return response()->json(['message' => 'Засварлагдлаа.', 'stamp' => $stamp->load('org:id,name')]);
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
        return response()->json(['stamp' => Stamp::with(['org:id,name', 'logs'])->findOrFail($id)]);
    }

    /** Жил бүрийн тамганы тохиргоо (эхлэх, дуусах огноо, загвар зураг) */
    public function yearsSave(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2012', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'design_image_url' => ['nullable', 'string', 'max:500'],
            'design_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $year = StampYear::firstOrNew(['year' => $data['year']]);
        $year->start_date = $data['start_date'];
        $year->end_date = $data['end_date'];

        if ($request->hasFile('design_image')) {
            $year->design_image = '/storage/' . $request->file('design_image')->store('uploads/stamps', 'public');
        } elseif (!empty($data['design_image_url'])) {
            $year->design_image = $data['design_image_url'];
        }

        $year->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'year' => $year]);
    }

    public function yearsDelete(int $id)
    {
        StampYear::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }
}
