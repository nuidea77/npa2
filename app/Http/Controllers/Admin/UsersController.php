<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\MailOutbox;
use App\Models\User;
use App\Support\Npa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /** Бүртгэл үүсгэсэн бүх хэрэглэгчийн жагсаалт */
    public function index(Request $request)
    {
        $q = User::with('org:id,name')->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }
        if ($request->filled('q')) {
            $s = $request->query('q');
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        return response()->json(['users' => $q->get()]);
    }

    /** Бүртгэл баталгаажуулах */
    public function approve(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        MailOutbox::create([
            'to' => $user->email,
            'subject' => 'NPA - Таны бүртгэл баталгаажлаа',
            'body' => "Сайн байна уу, {$user->name}. Таны NPA вэб дэх бүртгэл баталгаажлаа. Та одоо нэвтрэх боломжтой.",
        ]);

        return response()->json(['message' => 'Бүртгэл баталгаажлаа.', 'user' => $user]);
    }

    /** Бүртгэлээс татгалзах */
    public function reject(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return response()->json(['message' => 'Бүртгэлээс татгалзлаа.', 'user' => $user]);
    }

    /** Хэрэглэгчийн мэдээлэл засах (CMS) */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'last_name' => ['sometimes', 'string', 'max:100'],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'in:male,female'],
            'org_id' => ['sometimes', 'nullable', 'exists:orgs,id'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'emergency_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'email', 'max:190', 'unique:users,email,' . $id],
            'status' => ['sometimes', 'in:pending,active,rejected'],
        ]);

        $user->fill($data);
        if (isset($data['last_name']) || isset($data['first_name'])) {
            $user->name = trim(($user->last_name ?? '') . ' ' . ($user->first_name ?? ''));
        }
        $user->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'user' => $user->load('org:id,name')]);
    }

    /** Хэрэглэгч устгах */
    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Өөрийгөө устгах боломжгүй.'], 422);
        }
        $user->delete();

        return response()->json(['message' => 'Хэрэглэгч устгагдлаа.']);
    }

    /** Нууц үг сэргээх (супер админ) — түр нууц үг үүсгэнэ */
    public function resetPassword(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $temp = Str::random(8);
        $user->password = $temp;
        $user->save();

        MailOutbox::create([
            'to' => $user->email,
            'subject' => 'NPA - Нууц үг сэргээгдлээ',
            'body' => "Таны түр нууц үг: {$temp}. Нэвтэрсний дараа нууц үгээ солино уу.",
        ]);

        return response()->json([
            'message' => 'Түр нууц үг үүсгэгдлээ.',
            'temp_password' => $temp,
        ]);
    }

    /** Админд ирсэн мэдэгдлүүд */
    public function notifications()
    {
        return response()->json([
            'notifications' => AdminNotification::orderByDesc('id')->take(100)->get(),
            'unread' => AdminNotification::where('read', false)->count(),
        ]);
    }

    public function markNotificationRead(int $id)
    {
        AdminNotification::where('id', $id)->update(['read' => true]);
        return response()->json(['message' => 'OK']);
    }

    public function markAllNotificationsRead()
    {
        AdminNotification::where('read', false)->update(['read' => true]);
        return response()->json(['message' => 'OK']);
    }

    /** И-мэйл илгээлтийн бүртгэл (SMTP тохируулаагүй үеийн лог) */
    public function outbox()
    {
        return response()->json(['outbox' => MailOutbox::orderByDesc('id')->take(100)->get()]);
    }
}
