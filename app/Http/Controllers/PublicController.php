<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Faq;
use App\Models\JobPosting;
use App\Models\News;
use App\Models\Org;
use App\Models\Park;
use App\Models\Setting;
use App\Support\Npa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /** Нүүр хуудасны мэдээлэл */
    public function home()
    {
        $news = News::where('status', 'active')
            ->orderByDesc('published_at')->orderByDesc('id')
            ->take(4)->get()
            ->map(fn ($n) => $this->newsItem($n));

        $parks = Park::with('org:id,name')->where('featured', true)->take(4)
            ->get(['id', 'org_id', 'name', 'aimag', 'type', 'image', 'intro'])
            ->map(function ($p) {
                $p->intro = Str::limit((string) $p->intro, 160);
                return $p;
            });

        $events = Event::orderByDesc('year')->get()
            ->map(fn ($e) => $this->eventItem($e))
            ->filter(fn ($e) => in_array($e['reg_state'], ['open', 'not_started', 'soon']))
            ->take(3)->values();

        return response()->json([
            'news' => $news,
            'parks' => $parks,
            'events' => $events,
            'stats' => [
                'since' => 2012,
                'children' => '360+',
                'orgs' => Org::count(),
                'parks' => Park::count(),
            ],
        ]);
    }

    /** ТХГ хайх (аймгаар, нэрээр, төрлөөр) */
    public function parks(Request $request)
    {
        $q = Park::with('org:id,name');

        if ($request->filled('aimag')) {
            $q->where('aimag', $request->query('aimag'));
        }
        if ($request->filled('type')) {
            $q->where('type', $request->query('type'));
        }
        if ($request->filled('q')) {
            $q->where('name', 'like', '%' . $request->query('q') . '%');
        }

        $parks = $q->orderBy('name')
            ->get(['id', 'org_id', 'name', 'aimag', 'type', 'image', 'intro', 'featured'])
            ->map(function ($p) {
                $p->intro = Str::limit((string) $p->intro, 140);
                return $p;
            });

        return response()->json([
            'parks' => $parks,
            'aimags' => Npa::AIMAGS,
            'types' => Npa::PARK_TYPES,
        ]);
    }

    /** ТХГ-ын дэлгэрэнгүй */
    public function park(int $id)
    {
        $park = Park::with('org')->findOrFail($id);

        // Тухайн ХЗ-нд харьяалагдах бусад ТХГ-ууд
        $siblings = $park->org_id
            ? Park::where('org_id', $park->org_id)->where('id', '!=', $park->id)
                ->get(['id', 'name', 'aimag', 'type', 'image'])
            : collect();

        return response()->json(['park' => $park, 'siblings' => $siblings]);
    }

    /** ХЗ-дын жагсаалт (бүртгэлийн dropdown г.м) */
    public function orgs()
    {
        return response()->json([
            'orgs' => Org::orderBy('name')->get(['id', 'name', 'region', 'aimags', 'phone', 'email']),
        ]);
    }

    /** Мэдээний жагсаалт */
    public function news(Request $request)
    {
        $items = News::where('status', 'active')
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(9);

        return response()->json([
            'items' => collect($items->items())->map(fn ($n) => $this->newsItem($n)),
            'total' => $items->total(),
            'page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
        ]);
    }

    /** Мэдээний дэлгэрэнгүй */
    public function newsOne(int $id)
    {
        $n = News::where('status', 'active')->findOrFail($id);
        if ($n->type === 'external') {
            return response()->json(['news' => $this->newsItem($n)]);
        }
        return response()->json(['news' => [...$this->newsItem($n), 'body' => $n->body, 'body_en' => $n->body_en]]);
    }

    /** Нээлттэй ажлын байрны жагсаалт */
    public function jobs()
    {
        $jobs = JobPosting::with('org:id,name,phone,email')
            ->orderByRaw("case when status = 'open' then 0 else 1 end")
            ->orderByDesc('open_date')->get();

        return response()->json(['jobs' => $jobs]);
    }

    /** Ажлын байрны дэлгэрэнгүй */
    public function job(int $id)
    {
        return response()->json(['job' => JobPosting::with('org')->findOrFail($id)]);
    }

    /** Түгээмэл асуултууд */
    public function faqs()
    {
        return response()->json(['faqs' => Faq::orderBy('ord')->orderBy('id')->get()]);
    }

    /** Сургалт, арга хэмжээнүүд (хөтөлбөрийн хуудсууд) */
    public function events()
    {
        $events = Event::orderByDesc('year')->orderByDesc('id')->get()
            ->map(fn ($e) => $this->eventItem($e));

        return response()->json(['events' => $events]);
    }

    public function event(int $id)
    {
        $e = Event::findOrFail($id);
        return response()->json(['event' => [...$this->eventItem($e), 'questions' => $e->questions]]);
    }

    /** Нийтэд харагдах тохиргоо (цэс, санал хүсэлтийн төрөл, холбоо барих) */
    public function settings()
    {
        return response()->json([
            'menu' => Setting::get('menu', []),
            'feedback_types' => Setting::get('feedback_types', ['Санал, хүсэлт', 'Гомдол', 'Талархал']),
            'feedback_subtypes' => Setting::get('feedback_subtypes', ['Сайн дурын ажил', 'Ажлын зар', 'Бусад']),
            'contact' => [
                'facebook' => 'https://www.facebook.com/NationalParkAcademy',
                'instagram' => 'https://www.instagram.com/nationalparkacademy/',
                'phone' => '+976-70100426',
                'email' => 'info@mongolec.org',
                'address' => 'Монгол Экологи Төв, Далай Тауэр, 602 тоот, ЮНЕСКО гудамж-31, Сүхбаатар дүүрэг, 1-р хороо, Ш/Х-682, Улаанбаатар-14220',
            ],
            'positions' => Npa::POSITIONS,
            'aimags' => Npa::AIMAGS,
            'park_types' => Npa::PARK_TYPES,
            'volunteer_durations' => Npa::VOLUNTEER_DURATIONS,
        ]);
    }

    private function newsItem(News $n): array
    {
        return [
            'id' => $n->id,
            'title' => $n->title,
            'title_en' => $n->title_en,
            'image' => $n->image,
            'type' => $n->type,
            'external_url' => $n->external_url,
            'published_at' => optional($n->published_at)->format('Y-m-d'),
            'excerpt' => Str::limit(strip_tags((string) $n->body), 150),
        ];
    }

    private function eventItem(Event $e): array
    {
        return [
            'id' => $e->id,
            'program' => $e->program,
            'year' => $e->year,
            'title' => $e->title,
            'description' => $e->description,
            'reg_start' => optional($e->reg_start)->format('Y-m-d'),
            'reg_end' => optional($e->reg_end)->format('Y-m-d'),
            'login_required' => $e->login_required,
            'reg_state' => $e->regState(),
        ];
    }
}
