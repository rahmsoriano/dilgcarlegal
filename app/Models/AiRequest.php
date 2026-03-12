<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRequest extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'provider',
        'model',
        'status',
        'http_status',
        'error_type',
        'error_code',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'duration_ms',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
