<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Stamp;
use App\Models\StampYear;
use App\Models\Training;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Хэрэглэгчийн dashboard-ын бүх мэдээлэл */
    public function index(Request $request)
    {
        $user = $request->user()->load('org');

        // Өөрийн ХЗ-ны цуглуулсан NPA тамганууд (жил жилээр)
        $stampsByYear = [];
        $totalStamps = 0;
        $currentYearStamps = 0;

        if ($user->org_id) {
            $stamps = Stamp::where('org_id', $user->org_id)
                ->where('status', '!=', 'deleted')
                ->orderByDesc('stamp_date')
                ->get();

            $totalStamps = $stamps->count();
            $currentYearStamps = $stamps->where('year', (int) date('Y'))->count();

            $years = StampYear::orderByDesc('year')->get()->keyBy('year');

            foreach ($stamps->groupBy('year')->sortKeysDesc() as $year => $items) {
                $yearInfo = $years->get($year);
                $stampsByYear[] = [
                    'year' => $year,
                    'count' => $items->count(),
                    'design_image' => optional($yearInfo)->design_image,
                    'start_date' => optional(optional($yearInfo)->start_date)->format('Y-m-d'),
                    'end_date' => optional(optional($yearInfo)->end_date)->format('Y-m-d'),
                    'stamps' => $items->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'stamp_date' => $s->stamp_date->format('Y-m-d'),
                        'status' => $s->status,
                    ])->values(),
                ];
            }
        }

        // Өөрт үзэх эрхтэй сургалтууд (албан тушаал, ХЗ-ны бүсээр шүүгдэнэ)
        $trainings = Training::orderByDesc('published_at')->get()
            ->filter(function ($t) use ($user) {
                $posOk = empty($t->positions) || in_array($user->position, $t->positions);
                $regOk = empty($t->regions) || in_array(optional($user->org)->region, $t->regions);
                return $posOk && $regOk;
            })->values();

        // Удахгүй болох сургалт, арга хэмжээ
        $events = Event::orderByDesc('year')->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'program' => $e->program,
                'year' => $e->year,
                'title' => $e->title,
                'description' => $e->description,
                'reg_start' => optional($e->reg_start)->format('Y-m-d'),
                'reg_end' => optional($e->reg_end)->format('Y-m-d'),
                'reg_state' => $e->regState(),
            ])
            ->filter(fn ($e) => in_array($e['reg_state'], ['open', 'not_started', 'soon']))
            ->values();

        // Миний бүртгүүлсэн арга хэмжээнүүд
        $myRegs = EventRegistration::with('event:id,title,year')
            ->where('user_id', $user->id)
            ->orderByDesc('id')->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'event' => optional($r->event)->title,
                'registered_at' => $r->created_at->format('Y-m-d H:i'),
            ]);

        return response()->json([
            'user' => [...$user->toArray(), 'age' => $user->age(), 'is_admin' => $user->isAdmin()],
            'stamps' => [
                'total' => $totalStamps,
                'current_year' => $currentYearStamps,
                'max_per_year' => 50,
                'by_year' => $stampsByYear,
            ],
            'trainings' => $trainings,
            'events' => $events,
            'my_registrations' => $myRegs,
        ]);
    }

    /** Тамганы дэлгэрэнгүй (хэзээ, яагаад, юунд авсан) */
    public function stampDetail(Request $request, int $id)
    {
        $user = $request->user();
        $stamp = Stamp::with(['org:id,name', 'logs'])->findOrFail($id);

        if (!$user->isAdmin() && $stamp->org_id !== $user->org_id) {
            return response()->json(['message' => 'Хандах эрхгүй.'], 403);
        }

        return response()->json(['stamp' => $stamp]);
    }
}
