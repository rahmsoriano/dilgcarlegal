<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Law;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class LawController extends Controller
{
    public function index(Request $request)
    {
        $query = Law::query();
        $hasYearColumn = Schema::hasColumn('laws', 'year');
        $hasCategoryColumn = Schema::hasColumn('laws', 'category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('law_number', 'like', "%{$search}%")
                  ->orWhere('content_text', 'like', "%{$search}%");
            });
        }

        if ($hasYearColumn && $request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($hasCategoryColumn && $request->filled('category')) {
            $query->where('category', $request->category);
        }

        $laws = $query->latest()->paginate(10);
        
        $years = $hasYearColumn
            ? Law::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')
            : collect();
        $categories = $hasCategoryColumn
            ? Law::select('category')->distinct()->whereNotNull('category')->pluck('category')
            : collect();

        return view('admin.laws.index', compact('laws', 'years', 'categories'));
    }

    public function create()
    {
        return view('admin.laws.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'law_number' => 'required|string|max:255',
            'year' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $file = $request->file('file');
        $year = $request->year;
        $path = $file->storeAs("public/laws/{$year}", $file->getClientOriginalName());
        
        $contentText = $this->extractText($file);

        Law::create([
            'title' => $request->title,
            'law_number' => $request->law_number,
            'year' => $year,
            'category' => $request->category,
            'description' => $request->description,
            'file_path' => $path,
            'content_text' => $contentText,
        ]);

        return redirect()->route('admin.laws.index')->with('success', 'Law uploaded and processed successfully.');
    }

    public function edit(Law $law)
    {
        return view('admin.laws.edit', compact('law'));
    }

    public function update(Request $request, Law $law)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'law_number' => 'required|string|max:255',
            'year' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'law_number' => $request->law_number,
            'year' => $request->year,
            'category' => $request->category,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::exists($law->file_path)) {
                Storage::delete($law->file_path);
            }

            $file = $request->file('file');
            $year = $request->year;
            $path = $file->storeAs("public/laws/{$year}", $file->getClientOriginalName());
            
            $data['file_path'] = $path;
            $data['content_text'] = $this->extractText($file);
        }

        $law->update($data);

        return redirect()->route('admin.laws.index')->with('success', 'Law updated successfully.');
    }

    public function destroy(Law $law)
    {
        if (Storage::exists($law->file_path)) {
            Storage::delete($law->file_path);
        }

        $law->delete();

        return redirect()->route('admin.laws.index')->with('success', 'Law deleted successfully.');
    }

    private function extractText($file)
    {
        $extension = $file->getClientOriginalExtension();
        $text = '';

        try {
            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
            } elseif ($extension === 'txt') {
                $text = file_get_contents($file->getPathname());
            } elseif ($extension === 'docx') {
                $text = $this->extractDocxText($file->getPathname());
            }
            // Add more parsers if needed for .doc
        } catch (\Exception $e) {
            \Log::error('Text extraction failed: ' . $e->getMessage());
        }

        return $text;
    }

    private function extractDocxText($filePath)
    {
        $content = '';
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $xml = $zip->getFromIndex($index);
                $content = strip_tags($xml);
            }
            $zip->close();
        }
        return $content;
    }
}
