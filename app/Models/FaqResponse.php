<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqResponse extends Model
{
    protected $fillable = [
        'inquiry',
        'inquiry_normalized',
        'aliases',
        'response',
    ];

    protected $appends = [
        'alias_list',
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
        $t = str_replace(['’', "'", '`'], '', $t);
        $phraseMap = [
            'regional director' => 'regional_director',
            'current regional director' => 'current_regional_director',
            'present regional director' => 'current_regional_director',
            'kasalukuyang regional director' => 'current_regional_director',
        ];

        foreach ($phraseMap as $from => $to) {
            $t = str_replace($from, $to, $t);
        }

        $t = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $t) ?? $t;
        $t = preg_replace('/\s+/u', ' ', $t) ?? $t;
        $tokenMap = [
            'present' => 'current',
            'currently' => 'current',
            'kasalukuyang' => 'current',
            'ngayon' => 'current',
            'sino' => 'who',
            'anu' => 'what',
            'ano' => 'what',
            'paano' => 'how',
        ];

        $parts = preg_split('/\s+/u', trim($t)) ?: [];
        $parts = array_map(static function ($part) use ($tokenMap) {
            return $tokenMap[$part] ?? $part;
        }, $parts);

        $t = implode(' ', $parts);
        $t = trim($t);

        return mb_substr($t, 0, 255);
    }

    public function getAliasListAttribute(): array
    {
        return self::parseAliases((string) $this->aliases);
    }

    public static function parseAliases(string $aliases): array
    {
        $parts = preg_split('/\r\n|\r|\n/u', $aliases) ?: [];

        return array_values(array_filter(array_map(static fn ($item) => trim((string) $item), $parts), static fn ($item) => $item !== ''));
    }

    public function comparableQuestions(): array
    {
        return array_values(array_unique(array_filter([
            $this->inquiry,
            ...$this->alias_list,
        ], static fn ($value) => trim((string) $value) !== '')));
    }
}
