<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Npa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Шинэ бүртгэл үүсгэх хүсэлт (зөвхөн ТХГ-ын ажилтнууд) */
    public function register(Request $request)
    {
        if (User::where('email', $request->input('email'))->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Уучлаарай, Та бүртгэлтэй байна.',
            ]);
        }

        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:100', 'regex:' . Npa::CYRILLIC_REGEX],
            'first_name' => ['required', 'string', 'max:100', 'regex:' . Npa::CYRILLIC_REGEX],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'org_id' => ['required', 'exists:orgs,id'],
            'position' => ['required', 'in:' . implode(',', Npa::POSITIONS)],
            'phone' => ['required', 'regex:' . Npa::PHONE_REGEX],
            'emergency_phone' => ['nullable', 'regex:' . Npa::PHONE_REGEX],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'last_name.regex' => 'Овгоо кириллээр бичнэ үү.',
            'first_name.regex' => 'Нэрээ кириллээр бичнэ үү.',
            'phone.regex' => 'Утасны дугаар буруу бичиглэлтэй байна.',
            'emergency_phone.regex' => 'Утасны дугаар буруу бичиглэлтэй байна.',
            'email.email' => 'И-мэйл хаяг буруу бичиглэлтэй байна.',
            'password.confirmed' => 'Нууц үг таарахгүй байна.',
            'password.min' => 'Нууц үг доод тал нь 6 тэмдэгт байна.',
        ]);

        $user = User::create([
            ...$data,
            'name' => $data['last_name'] . ' ' . $data['first_name'],
            'role' => 'user',
            'status' => 'pending',
        ]);

        Npa::notifyAdmin(
            'user_register',
            $user->id,
            "Шинэ бүртгэлийн хүсэлт: {$user->name} ({$user->email})",
            'NPA вэб - Шинэ бүртгэлийн хүсэлт'
        );

        return response()->json([
            'message' => 'Таны бүртгэл үүсгэх хүсэлтийг амжилттай илгээлээ. Тантай бид эргэн холбогдох болно.',
            'note' => 'Таны бүртгэлийг ажлын 72 цагийн дотор баталгаажуулж, хариу өгнө.',
        ], 201);
    }

    /** Нэвтрэх (нэвтрэх нэр нь и-мэйл хаяг) */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'И-мэйл хаяг эсвэл нууц үг буруу байна.',
            ]);
        }

        if ($user->status === 'pending') {
            throw ValidationException::withMessages([
                'email' => 'Таны бүртгэл баталгаажаагүй байна. Бид ажлын 72 цагийн дотор баталгаажуулж, хариу өгнө.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Таны бүртгэл идэвхгүй байна. Админтай холбогдоно уу.',
            ]);
        }

        Auth::login($user, (bool) ($data['remember'] ?? true));
        $request->session()->regenerate();

        return $this->me($request);
    }

    /** Гарах */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Системээс гарлаа.']);
    }

    /** Нэвтэрсэн хэрэглэгчийн мэдээлэл */
    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['user' => null]);
        }
        $user->load('org');

        return response()->json([
            'user' => [
                ...$user->toArray(),
                'age' => $user->age(),
                'is_admin' => $user->isAdmin(),
            ],
        ]);
    }

    /** Өөрийн мэдээлэл шинэчлэх (нууц үгээ оруулснаар баталгаажна) */
    public function updateMe(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id],
            'position' => ['required', 'in:' . implode(',', Npa::POSITIONS)],
            'phone' => ['required', 'regex:' . Npa::PHONE_REGEX],
            'emergency_phone' => ['nullable', 'regex:' . Npa::PHONE_REGEX],
            'current_password' => ['required', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'phone.regex' => 'Утасны дугаар буруу бичиглэлтэй байна.',
            'email.email' => 'И-мэйл хаяг буруу бичиглэлтэй байна.',
            'email.unique' => 'Энэ и-мэйл хаяг өөр бүртгэлд ашиглагдаж байна.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Нууц үг буруу байна.',
            ]);
        }

        $user->email = $data['email'];
        $user->position = $data['position'];
        $user->phone = $data['phone'];
        $user->emergency_phone = $data['emergency_phone'] ?? $user->emergency_phone;
        if (!empty($data['new_password'])) {
            $user->password = $data['new_password'];
        }
        $user->save();

        return response()->json(['message' => 'Мэдээлэл амжилттай шинэчлэгдлээ.']);
    }

    /** Профайл зураг оруулах */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ], ['photo.max' => 'Зургийн хэмжээ 2MB-с бага байх шаардлагатай.']);

        $path = $request->file('photo')->store('uploads/avatars', 'public');
        $user = $request->user();
        $user->photo = '/storage/' . $path;
        $user->save();

        return response()->json(['photo' => $user->photo]);
    }

    /** Нууц үг мартсан */
    public function forgot(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $data['email'])->first();
        if ($user) {
            Npa::notifyAdmin(
                'password_reset',
                $user->id,
                "Нууц үг сэргээх хүсэлт: {$user->name} ({$user->email})",
                'NPA вэб - Нууц үг сэргээх хүсэлт'
            );
        }

        return response()->json([
            'message' => 'Хэрэв энэ и-мэйл хаяг бүртгэлтэй бол нууц үг сэргээх хүсэлт админд илгээгдлээ. Супер админ таны нууц үгийг сэргээж, и-мэйлээр мэдэгдэх болно.',
        ]);
    }
}
