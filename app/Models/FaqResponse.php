<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqResponse extends Model
{
    protected $fillable = [
        'inquiry',
        'inquiry_normalized',
        'response',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $model->inquiry_normalized = self::normalizeInquiry($model->inquiry);
        });
    }

    public static function normalizeInquiry(string $text): string
    {
        $t = mb_strtolower(trim($text));
        $t = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $t) ?? $t;
        $t = preg_replace('/\s+/u', ' ', $t) ?? $t;
        $t = trim($t);

        return mb_substr($t, 0, 255);
    }
}

