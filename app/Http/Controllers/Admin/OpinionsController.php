<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DilgOpinion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OpinionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $opinions = DilgOpinion::query()
            ->when($q !== '', fn ($query) => $query->search($q))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.opinions.index', [
            'opinions' => $opinions,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.opinions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'opinion_date' => ['nullable', 'date'],
            'tags' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:dilg_opinions,slug'],
            'body' => ['required', 'string'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug(Str::limit($validated['title'], 60, ''));
        }

        $opinion = DilgOpinion::create($validated);

        return redirect()->route('admin.opinions.index').'#opinion-'.$opinion->id;
    }
}
