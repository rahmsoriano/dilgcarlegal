<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalOpinionLibrary;

class OpinionsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $opinions = LegalOpinionLibrary::query()
            ->when($q !== '', fn ($query) => $query->search($q))
            ->orderByDesc('date')
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
            'title' => ['required', 'string', 'max:255'],
            'opinion_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'context' => ['required', 'string'],
        ]);

        $opinion = LegalOpinionLibrary::create($validated);

        return redirect()->route('admin.opinions.show', $opinion);
    }

    public function show(LegalOpinionLibrary $opinion)
    {
        return view('admin.opinions.show', [
            'opinion' => $opinion,
        ]);
    }

    public function edit(LegalOpinionLibrary $opinion)
    {
        return view('admin.opinions.edit', [
            'opinion' => $opinion,
        ]);
    }

    public function update(Request $request, LegalOpinionLibrary $opinion)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'opinion_number' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'context' => ['required', 'string'],
        ]);

        $opinion->update($validated);

        return redirect()->route('admin.opinions.show', $opinion);
    }

    public function destroy(LegalOpinionLibrary $opinion)
    {
        $opinion->delete();

        return redirect()->route('admin.opinions.index');
    }
}
