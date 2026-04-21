<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'is_saved',
        'is_pinned',
        'pinned_at',
        'saved_at',
        'last_message_at',
    ];

    protected $casts = [
        'is_saved' => 'boolean',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
        'saved_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
