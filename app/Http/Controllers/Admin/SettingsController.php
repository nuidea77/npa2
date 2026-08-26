<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\News;
use App\Models\Setting;
use App\Models\User;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /** Админ нүүрний тоон үзүүлэлт */
    public function stats()
    {
        return response()->json([
            'pending_users' => User::where('status', 'pending')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'unanswered_feedback' => Feedback::where('status', 'unanswered')->count(),
            'new_volunteers' => VolunteerRequest::where('status', 'new')->count(),
            'news' => News::count(),
            'events' => Event::count(),
            'unread_notifications' => AdminNotification::where('read', false)->count(),
        ]);
    }

    /** Тохиргоо унших */
    public function index()
    {
        return response()->json([
            'menu' => Setting::get('menu', []),
            'feedback_types' => Setting::get('feedback_types', []),
            'feedback_subtypes' => Setting::get('feedback_subtypes', []),
        ]);
    }

    /** Тохиргоо хадгалах (цэсний харагдац, санал хүсэлтийн төрлүүд) */
    public function save(Request $request)
    {
        $data = $request->validate([
            'menu' => ['nullable', 'array'],
            'feedback_types' => ['nullable', 'array'],
            'feedback_subtypes' => ['nullable', 'array'],
        ]);

        foreach (['menu', 'feedback_types', 'feedback_subtypes'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key]);
            }
        }

        return response()->json(['message' => 'Хадгалагдлаа.']);
    }

    /** Админ эрхтэй хэрэглэгчид (зөвхөн супер админ удирдана) */
    public function admins()
    {
        return response()->json([
            'admins' => User::whereIn('role', ['admin', 'superadmin'])->get(['id', 'name', 'email', 'role', 'status']),
        ]);
    }

    /** Хэрэглэгчид админ эрх олгох/хасах */
    public function setRole(Request $request, int $id)
    {
        $data = $request->validate(['role' => ['required', 'in:user,admin,superadmin']]);

        $user = User::findOrFail($id);
        if ($user->id === $request->user()->id && $data['role'] === 'user') {
            return response()->json(['message' => 'Өөрийн супер админ эрхийг хасах боломжгүй.'], 422);
        }
        $user->role = $data['role'];
        $user->save();

        return response()->json(['message' => 'Эрх шинэчлэгдлээ.', 'user' => $user]);
    }
}
