<?php

namespace Tests\Feature;

use App\Models\LegalOpinionLibrary;
use App\Services\OpinionRetriever;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpinionRetrieverTest extends TestCase
{
    use RefreshDatabase;

    public function test_retriever_prioritizes_latest_related_legal_opinion(): void
    {
        $older = LegalOpinionLibrary::create([
            'title' => 'Older SK Chairperson Qualification Opinion',
            'opinion_number' => 'Opinion No. 01, s. 2020',
            'date' => '2020-01-15',
            'keywords' => 'SK chairperson qualification age',
            'context' => 'This legal opinion discusses SK chairperson qualification and the required age for Sangguniang Kabataan officials.',
        ]);

        $latest = LegalOpinionLibrary::create([
            'title' => 'Latest SK Chairperson Qualification Opinion',
            'opinion_number' => 'Opinion No. 99, s. 2026',
            'date' => '2026-05-01',
            'keywords' => 'SK chairperson qualification age',
            'context' => 'This latest legal opinion discusses SK chairperson qualification and the required age for Sangguniang Kabataan officials.',
        ]);

        $items = app(OpinionRetriever::class)->retrieve('What is the age qualification for SK chairperson?', 5);

        $this->assertNotEmpty($items);
        $this->assertSame($latest->id, $items[0]['id']);
        $this->assertContains($older->id, collect($items)->pluck('id')->all());
    }
}
