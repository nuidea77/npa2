<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /** Сургалт, арга хэмжээний жагсаалт (бүртгэлийн тоотой) */
    public function index()
    {
        $events = Event::withCount('registrations')->orderByDesc('year')->orderByDesc('id')->get()
            ->map(fn ($e) => [...$e->toArray(), 'reg_state' => $e->regState()]);

        return response()->json(['events' => $events]);
    }

    /** Арга хэмжээ нэмэх, засах (асуулгын асуултууд, огноо, статус) */
    public function save(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'program' => ['required', 'in:khuraldai,junior_ranger,regional'],
            'year' => ['required', 'integer', 'min:2012', 'max:2100'],
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'reg_start' => ['nullable', 'date'],
            'reg_end' => ['nullable', 'date'],
            'status' => ['required', 'in:auto,open,closed,soon'],
            'login_required' => ['required', 'boolean'],
            'questions' => ['nullable', 'array'],
            'questions.*.id' => ['required', 'string', 'max:50'],
            'questions.*.type' => ['required', 'in:boolean,text,link,image,single,multi,personal'],
            'questions.*.label' => ['nullable', 'string', 'max:1000'],
            'questions.*.required' => ['nullable', 'boolean'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.maxWords' => ['nullable', 'integer', 'min:1'],
        ]);

        $event = !empty($data['id']) ? Event::findOrFail($data['id']) : new Event();
        $event->fill(collect($data)->except('id')->toArray())->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'event' => $event]);
    }

    public function delete(int $id)
    {
        Event::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    /** Тухайн арга хэмжээний бүртгэлүүд (асуулгын хариултууд) */
    public function registrations(int $id)
    {
        $event = Event::findOrFail($id);
        $regs = EventRegistration::with('user:id,name,email,org_id,position')
            ->where('event_id', $id)->orderByDesc('id')->get();

        return response()->json(['event' => $event, 'registrations' => $regs]);
    }
}
