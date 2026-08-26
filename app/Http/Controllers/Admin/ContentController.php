<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\JobPosting;
use App\Models\News;
use App\Models\Org;
use App\Models\Park;
use App\Models\Training;
use Illuminate\Http\Request;

/**
 * Вэбийн үндсэн агуулгыг удирдах: мэдээ, ажлын байр, ТХГ, ХЗ, FAQ, сургалт.
 */
class ContentController extends Controller
{
    // ---------- Мэдээ ----------

    public function newsIndex()
    {
        return response()->json(['news' => News::orderByDesc('published_at')->orderByDesc('id')->get()]);
    }

    public function newsSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:300'],
            'title_en' => ['nullable', 'string', 'max:300'],
            'body' => ['nullable', 'string'],
            'body_en' => ['nullable', 'string'],
            'type' => ['required', 'in:internal,external'],
            'external_url' => ['nullable', 'url', 'required_if:type,external'],
            'published_at' => ['required', 'date'],
            'status' => ['required', 'in:active,hidden'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], ['external_url.required_if' => 'Гадаад линк төрөлтэй мэдээнд линк заавал оруулна.']);

        $news = !empty($data['id']) ? News::findOrFail($data['id']) : new News();
        $news->fill(collect($data)->except(['id', 'image', 'image_url'])->toArray());

        if ($request->hasFile('image')) {
            $news->image = '/storage/' . $request->file('image')->store('uploads/news', 'public');
        } elseif (array_key_exists('image_url', $data) && $data['image_url'] !== null) {
            $news->image = $data['image_url'];
        }

        $news->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'news' => $news]);
    }

    public function newsDelete(int $id)
    {
        News::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    // ---------- Ажлын байр ----------

    public function jobsIndex()
    {
        return response()->json(['jobs' => JobPosting::with('org:id,name')->orderByDesc('id')->get()]);
    }

    public function jobsSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'org_id' => ['nullable', 'exists:orgs,id'],
            'park_name' => ['nullable', 'string', 'max:190'],
            'position' => ['required', 'string', 'max:190'],
            'contract_type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:open,closed'],
            'open_date' => ['nullable', 'date'],
            'close_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
        ]);

        $job = !empty($data['id']) ? JobPosting::findOrFail($data['id']) : new JobPosting();
        $job->fill(collect($data)->except('id')->toArray())->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'job' => $job->load('org:id,name')]);
    }

    public function jobsDelete(int $id)
    {
        JobPosting::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    // ---------- ХЗ (Хамгаалалтын захиргаа) ----------

    public function orgsIndex()
    {
        return response()->json(['orgs' => Org::withCount('parks')->orderBy('name')->get()]);
    }

    public function orgsSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:300'],
            'region' => ['nullable', 'string', 'max:100'],
            'aimags' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:500'],
            'intro' => ['nullable', 'string'],
            'accepts_volunteers' => ['nullable', 'boolean'],
            'volunteer_durations' => ['nullable', 'array'],
        ]);

        $org = !empty($data['id']) ? Org::findOrFail($data['id']) : new Org();
        $org->fill(collect($data)->except('id')->toArray())->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'org' => $org]);
    }

    public function orgsDelete(int $id)
    {
        Org::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    // ---------- ТХГ (Тусгай хамгаалалттай газар) ----------

    public function parksIndex()
    {
        return response()->json(['parks' => Park::with('org:id,name')->orderBy('name')->get()]);
    }

    public function parksSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'org_id' => ['nullable', 'exists:orgs,id'],
            'name' => ['required', 'string', 'max:300'],
            'aimag' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:100'],
            'intro' => ['nullable', 'string'],
            'highlights' => ['nullable', 'string'],
            'animals' => ['nullable', 'string'],
            'geography' => ['nullable', 'string'],
            'locals' => ['nullable', 'string'],
            'get_there' => ['nullable', 'string'],
            'travel' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'warnings' => ['nullable', 'string'],
            'admin_info' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'featured' => ['nullable', 'boolean'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $park = !empty($data['id']) ? Park::findOrFail($data['id']) : new Park();
        $park->fill(collect($data)->except(['id', 'image', 'image_url'])->toArray());

        if ($request->hasFile('image')) {
            $park->image = '/storage/' . $request->file('image')->store('uploads/parks', 'public');
        } elseif (array_key_exists('image_url', $data) && $data['image_url'] !== null) {
            $park->image = $data['image_url'];
        }

        $park->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'park' => $park->load('org:id,name')]);
    }

    public function parksDelete(int $id)
    {
        Park::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    // ---------- FAQ ----------

    public function faqIndex()
    {
        return response()->json(['faqs' => Faq::orderBy('ord')->orderBy('id')->get()]);
    }

    public function faqSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'question_en' => ['nullable', 'string'],
            'answer_en' => ['nullable', 'string'],
            'ord' => ['nullable', 'integer'],
        ]);

        $faq = !empty($data['id']) ? Faq::findOrFail($data['id']) : new Faq();
        $faq->fill(collect($data)->except('id')->toArray())->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'faq' => $faq]);
    }

    public function faqDelete(int $id)
    {
        Faq::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }

    // ---------- Сургалтын материал ----------

    public function trainingsIndex()
    {
        return response()->json(['trainings' => Training::orderByDesc('published_at')->get()]);
    }

    public function trainingsSave(Request $request)
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:300'],
            'type' => ['required', 'in:youtube,pdf,image'],
            'url' => ['nullable', 'string', 'max:500'],
            'summary' => ['nullable', 'string'],
            'published_at' => ['required', 'date'],
            'positions' => ['nullable', 'array'],
            'regions' => ['nullable', 'array'],
            'cover_url' => ['nullable', 'string', 'max:500'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $t = !empty($data['id']) ? Training::findOrFail($data['id']) : new Training();
        $t->fill(collect($data)->except(['id', 'cover', 'cover_url', 'file'])->toArray());

        if ($request->hasFile('file')) {
            $t->url = '/storage/' . $request->file('file')->store('uploads/trainings', 'public');
        }
        if ($request->hasFile('cover')) {
            $t->cover = '/storage/' . $request->file('cover')->store('uploads/trainings', 'public');
        } elseif (array_key_exists('cover_url', $data) && $data['cover_url'] !== null) {
            $t->cover = $data['cover_url'];
        }

        $t->save();

        return response()->json(['message' => 'Хадгалагдлаа.', 'training' => $t]);
    }

    public function trainingsDelete(int $id)
    {
        Training::where('id', $id)->delete();
        return response()->json(['message' => 'Устгагдлаа.']);
    }
}
