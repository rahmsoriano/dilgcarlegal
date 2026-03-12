<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'response_meta',
    ];

    protected $casts = [
        'response_meta' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
