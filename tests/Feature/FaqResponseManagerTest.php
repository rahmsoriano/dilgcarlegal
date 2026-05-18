<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\AmicusSection;
use App\Models\FaqResponse;
use App\Models\User;
use App\Services\FaqResponseMatcher;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqResponseManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_faq_response_manager(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.faq-responses.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_faq_response_manager(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('admin.faq-responses.index'))
            ->assertForbidden();
    }

    public function test_chatbot_uses_faq_response_when_match_found(): void
    {
        FaqResponse::create([
            'inquiry' => 'reset password',
            'aliases' => "forgot password\nHow do I change my password?",
            'response' => 'Use the Forgot password link on the login screen.',
        ]);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => null,
            'is_saved' => false,
            'is_pinned' => false,
        ]);

        $resp = $this->actingAs($user)
            ->postJson(route('messages.store', $conversation), [
                'prompt' => 'How do I RESET my password please?',
            ])
            ->assertOk()
            ->json();

        $this->assertStringContainsString(
            'Use the Forgot password link on the login screen.',
            $resp['assistant_message']['content'] ?? ''
        );
    }

    public function test_faq_matching_understands_case_and_equivalent_wording(): void
    {
        $faq = FaqResponse::create([
            'inquiry' => 'Who is the current Regional Director?',
            'aliases' => '',
            'response' => 'The current Regional Director is listed in the official office profile.',
        ]);

        $matcher = app(FaqResponseMatcher::class);

        foreach ([
            'Who is the current Regional Director?',
            'WHO IS THE PRESENT REGIONAL DIRECTOR?',
            'Sino ang kasalukuyang Regional Director?',
            'who heads the regional office?',
        ] as $prompt) {
            $match = $matcher->findBestMatch($prompt);

            $this->assertNotNull($match, 'Expected FAQ match for: '.$prompt);
            $this->assertSame($faq->id, $match->id, 'Expected equivalent wording to match the same FAQ.');
        }
    }

    public function test_chatbot_formats_faq_response_for_readability(): void
    {
        FaqResponse::create([
            'inquiry' => 'office requirements',
            'aliases' => '',
            'response' => "Requirements:\n- Valid ID\n- Signed request\n\nSteps:\n1. Submit the form\n2. Wait for confirmation\n\nSource: https://example.com/guide",
        ]);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => null,
            'is_saved' => false,
            'is_pinned' => false,
        ]);

        $content = $this->actingAs($user)
            ->postJson(route('messages.store', $conversation), [
                'prompt' => 'What are the office requirements?',
            ])
            ->assertOk()
            ->json('assistant_message.content');

        $this->assertStringContainsString('class="chat-faq-answer"', $content);
        $this->assertStringContainsString('<div class="chat-faq-heading">Requirements</div>', $content);
        $this->assertStringContainsString('<ul class="chat-faq-list"><li>Valid ID</li><li>Signed request</li></ul>', $content);
        $this->assertStringContainsString('<ol class="chat-faq-list"><li>Submit the form</li><li>Wait for confirmation</li></ol>', $content);
        $this->assertStringContainsString('class="external-source-link"', $content);
    }

    public function test_new_chat_does_not_create_empty_conversation_for_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->assertSame(0, Conversation::count());

        $this->actingAs($admin)
            ->get(route('admin.legal.ai.new'))
            ->assertOk();

        $this->assertSame(0, Conversation::count());
    }

    public function test_new_chat_does_not_create_empty_conversation_for_public_session(): void
    {
        $this->get(route('legal.ai.new'))
            ->assertOk()
            ->assertSessionMissing('public_conversations');
    }

    public function test_restricted_admin_cannot_access_amicus_routes(): void
    {
        $restrictedAdmin = User::factory()->create(['role' => User::ROLE_ADMIN_RESTRICTED]);

        $this->actingAs($restrictedAdmin)
            ->get(route('admin.amicus.index'))
            ->assertForbidden();
    }

    public function test_full_admin_can_access_amicus_routes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.amicus.index'))
            ->assertOk();
    }

    public function test_chatbot_uses_amicus_sections_as_knowledge_base(): void
    {
        AmicusSection::create([
            'section_title' => 'Barangay Assembly Procedure',
            'category' => 'Barangay Governance',
            'section_content' => "Barangay assemblies should be conducted with proper notice, agenda preparation, and documentation of proceedings.",
        ]);

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => null,
            'is_saved' => false,
            'is_pinned' => false,
        ]);

        $content = $this->actingAs($user)
            ->postJson(route('messages.store', $conversation), [
                'prompt' => 'What is the barangay assembly procedure?',
            ])
            ->assertOk()
            ->json('assistant_message.content');

        $this->assertStringContainsString('AMICUS Knowledge Base', $content);
        $this->assertStringContainsString('Barangay Assembly Procedure', $content);
        $this->assertStringContainsString('proper notice', $content);
    }

    public function test_document_review_accepts_uploaded_text_file(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $file = UploadedFile::fake()->createWithContent('memo.txt', "Memorandum\nRegional Director\nKey obligations are listed here.");

        $this->actingAs($user)
            ->post(route('document-review.store'), [
                'document' => $file,
                'focus' => 'summary',
            ])
            ->assertOk()
            ->assertJsonStructure(['title', 'review']);
    }
}
