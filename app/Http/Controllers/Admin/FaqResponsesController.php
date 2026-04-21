<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqResponse;
use Illuminate\Http\Request;

class FaqResponsesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = FaqResponse::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('inquiry', 'like', '%'.$q.'%')
                    ->orWhere('response', 'like', '%'.$q.'%');
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.faq-responses.index', [
            'items' => $items,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inquiry' => ['required', 'string', 'max:1000'],
            'response' => ['required', 'string', 'max:8000'],
        ]);

        FaqResponse::create([
            'inquiry' => $validated['inquiry'],
            'response' => $validated['response'],
        ]);

        return redirect()->route('admin.faq-responses.index');
    }

    public function edit(FaqResponse $faqResponse)
    {
        return view('admin.faq-responses.edit', [
            'item' => $faqResponse,
        ]);
    }

    public function update(Request $request, FaqResponse $faqResponse)
    {
        $validated = $request->validate([
            'inquiry' => ['required', 'string', 'max:1000'],
            'response' => ['required', 'string', 'max:8000'],
        ]);

        $faqResponse->update([
            'inquiry' => $validated['inquiry'],
            'response' => $validated['response'],
        ]);

        return redirect()->route('admin.faq-responses.index');
    }

    public function destroy(FaqResponse $faqResponse)
    {
        $faqResponse->delete();

        return redirect()->route('admin.faq-responses.index');
    }
}

