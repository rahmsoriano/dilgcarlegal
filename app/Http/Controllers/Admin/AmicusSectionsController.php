<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmicusSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmicusSectionsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $items = AmicusSection::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery
                        ->where('section_title', 'like', '%'.$q.'%')
                        ->orWhere('category', 'like', '%'.$q.'%')
                        ->orWhere('section_content', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.amicus.index', [
            'items' => $items,
            'q' => $q,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        AmicusSection::create($validated);

        return redirect()
            ->route('admin.amicus.index')
            ->with('status', 'AMICUS section added successfully.');
    }

    public function edit(AmicusSection $amicus): View
    {
        return view('admin.amicus.edit', [
            'amicus' => $amicus,
        ]);
    }

    public function update(Request $request, AmicusSection $amicus): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $amicus->update($validated);

        return redirect()
            ->route('admin.amicus.index')
            ->with('status', 'AMICUS section updated successfully.');
    }

    public function destroy(AmicusSection $amicus): RedirectResponse
    {
        $amicus->delete();

        return redirect()
            ->route('admin.amicus.index')
            ->with('status', 'AMICUS section deleted successfully.');
    }

    /**
     * @return array<string, string|null>
     */
    protected function validatePayload(Request $request): array
    {
        /** @var array<string, string|null> $validated */
        $validated = $request->validate([
            'section_title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'section_content' => ['required', 'string'],
        ]);

        if (isset($validated['category'])) {
            $validated['category'] = trim((string) $validated['category']) !== ''
                ? trim((string) $validated['category'])
                : null;
        }

        return $validated;
    }
}
