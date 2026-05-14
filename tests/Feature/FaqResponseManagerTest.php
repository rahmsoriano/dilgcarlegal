<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\FaqResponse;
use App\Models\User;
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
