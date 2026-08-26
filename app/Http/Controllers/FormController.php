<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Feedback;
use App\Models\Org;
use App\Models\VolunteerRequest;
use App\Support\Npa;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    /** Санал хүсэлт илгээх */
    public function feedback(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'subtype' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'regex:' . Npa::PHONE_REGEX],
        ], [
            'email.email' => 'И-мэйл хаяг буруу бичиглэлтэй байна.',
            'phone.regex' => 'Утасны дугаар буруу бичиглэлтэй байна.',
        ]);

        $fb = Feedback::create([
            ...$data,
            'user_id' => optional($request->user())->id,
        ]);

        Npa::notifyAdmin(
            'feedback',
            $fb->id,
            "Шинэ санал хүсэлт ({$fb->type}): " . mb_substr($fb->message, 0, 80),
            'NPA вэб - Шинэ санал хүсэлт'
        );

        return response()->json([
            'message' => 'Таны санал хүсэлтийг хүлээн авлаа. Бид тантай эргэн холбогдох болно.',
        ], 201);
    }

    /** Сайн дурын ажил хийх ХЗ хайх */
    public function volunteerSearch(Request $request)
    {
        $q = Org::query()->where('accepts_volunteers', true);

        if ($request->filled('aimag')) {
            $aimag = $request->query('aimag');
            $q->where(function ($w) use ($aimag) {
                $w->whereJsonContains('aimags', $aimag)
                  ->orWhereHas('parks', fn ($p) => $p->where('aimag', $aimag));
            });
        }
        if ($request->filled('park')) {
            $park = $request->query('park');
            $q->where(function ($w) use ($park) {
                $w->where('name', 'like', "%{$park}%")
                  ->orWhereHas('parks', fn ($p) => $p->where('name', 'like', "%{$park}%"));
            });
        }
        if ($request->filled('duration')) {
            $q->whereJsonContains('volunteer_durations', $request->query('duration'));
        }

        $orgs = $q->with('parks:id,org_id,name,aimag')
            ->get(['id', 'name', 'region', 'aimags', 'phone', 'email', 'address', 'intro', 'volunteer_durations']);

        return response()->json(['orgs' => $orgs]);
    }

    /** Сайн дурын ажлын хүсэлт илгээх */
    public function volunteerRequest(Request $request)
    {
        $data = $request->validate([
            'org_id' => ['nullable', 'exists:orgs,id'],
            'duration' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:190'],
            'phone' => ['required', 'regex:' . Npa::PHONE_REGEX],
            'email' => ['required', 'email', 'max:190'],
            'intro' => ['required', 'string', 'max:3000'],
            'reason' => ['required', 'string', 'max:3000'],
        ], [
            'phone.regex' => 'Утасны дугаар буруу бичиглэлтэй байна.',
            'email.email' => 'И-мэйл хаяг буруу бичиглэлтэй байна.',
        ]);

        $vr = VolunteerRequest::create($data);

        Npa::notifyAdmin(
            'volunteer',
            $vr->id,
            "Сайн дурын ажлын хүсэлт: {$vr->name} ({$vr->email})",
            'NPA вэб - Сайн дурын ажлын хүсэлт'
        );

        return response()->json([
            'message' => 'Таны хүсэлтийг хүлээн авлаа. Бид тантай эргэн холбогдох болно.',
        ], 201);
    }

    /** Сургалт, арга хэмжээнд бүртгүүлэх (асуулга бөглөх) */
    public function eventRegister(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $state = $event->regState();

        if ($state === 'not_started' || $state === 'soon') {
            return response()->json(['message' => 'Бүртгэл эхлээгүй байна.'], 422);
        }
        if ($state === 'closed') {
            return response()->json(['message' => 'Бүртгэл дууссан байна.'], 422);
        }

        $user = $request->user();
        if ($event->login_required && !$user) {
            return response()->json(['message' => 'Нэвтрэх шаардлагатай.', 'login_required' => true], 401);
        }

        $answers = json_decode((string) $request->input('answers', '{}'), true) ?: [];
        $questions = $event->questions ?? [];
        $errors = [];
        $files = [];

        foreach ($questions as $qn) {
            $qid = $qn['id'];
            $type = $qn['type'] ?? 'text';
            $required = (bool) ($qn['required'] ?? false);
            $value = $answers[$qid] ?? null;

            if ($type === 'image') {
                $file = $request->file('file_' . $qid);
                if ($file) {
                    $request->validate(
                        ['file_' . $qid => ['file', 'max:4096', 'mimes:jpg,jpeg,png,gif,webp,pdf']],
                        ['file_' . $qid . '.max' => 'Файлын хэмжээ 4MB-с бага байх шаардлагатай.']
                    );
                    $files[$qid] = '/storage/' . $file->store('uploads/events', 'public');
                } elseif ($required) {
                    $errors[$qid] = 'Файл оруулна уу.';
                }
                continue;
            }

            $empty = $value === null || $value === '' || (is_array($value) && count($value) === 0);
            if ($required && $empty) {
                $errors[$qid] = 'Энэ талбарыг бөглөнө үү.';
                continue;
            }

            if (!$empty && !empty($qn['maxWords']) && is_string($value)) {
                $wordCount = count(preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY));
                if ($wordCount > (int) $qn['maxWords']) {
                    $errors[$qid] = 'Хариулт хамгийн ихдээ ' . $qn['maxWords'] . ' үгтэй байна.';
                }
            }

            if (!$empty && $type === 'link' && is_string($value) && !preg_match('~^https?://~i', $value)) {
                $errors[$qid] = 'Зөв линк оруулна уу (https://...).';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $reg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => optional($user)->id,
            'data' => $answers,
            'files' => $files,
        ]);

        $who = $user ? $user->name : ($answers['first_name'] ?? 'Зочин');
        Npa::notifyAdmin(
            'event_reg',
            $reg->id,
            "«{$event->title}» бүртгэл: {$who}",
            'NPA вэб - Арга хэмжээний шинэ бүртгэл'
        );

        return response()->json([
            'message' => 'Таны бүртгэлийг хүлээн авлаа. Бид тантай эргэн холбогдох болно.',
        ], 201);
    }
}
