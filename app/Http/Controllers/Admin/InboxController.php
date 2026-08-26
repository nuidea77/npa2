<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    /** Ирсэн санал хүсэлтүүд (шүүлттэй) */
    public function feedback(Request $request)
    {
        $q = Feedback::with('user:id,name,email,org_id')->orderByDesc('id');

        if ($request->filled('type')) {
            $q->where('type', $request->query('type'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }

        return response()->json(['feedback' => $q->get()]);
    }

    /** Санал хүсэлтэд хариу өгсөн/өгөөгүй статус, хариу */
    public function feedbackUpdate(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:unanswered,answered'],
            'reply' => ['nullable', 'string', 'max:5000'],
        ]);

        $fb = Feedback::findOrFail($id);
        $fb->fill($data)->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'feedback' => $fb]);
    }

    public function feedbackDelete(int $id)
    {
        Feedback::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    /** Сайн дурын ажлын хүсэлтүүд */
    public function volunteers(Request $request)
    {
        $q = VolunteerRequest::with('org:id,name')->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }

        return response()->json(['volunteers' => $q->get()]);
    }

    public function volunteerUpdate(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:new,replied']]);
        $vr = VolunteerRequest::findOrFail($id);
        $vr->fill($data)->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'volunteer' => $vr]);
    }
}
