<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\FaqResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqResponseManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_faq_response_manager(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.faq-responses.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_access_faq_response_manager(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.faq-responses.index'))
            ->assertForbidden();
    }

    public function test_chatbot_uses_faq_response_when_match_found(): void
    {
        FaqResponse::create([
            'inquiry' => 'reset password',
            'response' => 'Use the Forgot password link on the login screen.',
        ]);

        $user = User::factory()->create(['is_admin' => false]);
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

        $this->assertSame(
            'Use the Forgot password link on the login screen.',
            $resp['assistant_message']['content'] ?? null
        );
    }

    public function test_new_chat_does_not_create_empty_conversation_for_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

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
}
