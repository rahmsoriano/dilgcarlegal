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
            'who heads the regional office' => 'who is current_regional_director',
            'who heads regional office' => 'who is current_regional_director',
            'who leads the regional office' => 'who is current_regional_director',
            'who leads regional office' => 'who is current_regional_director',
            'head of the regional office' => 'current_regional_director',
            'head of regional office' => 'current_regional_director',
            'regional office director' => 'regional_director',
            'director of the region' => 'regional_director',
            'director of region' => 'regional_director',
            'head of the region' => 'regional_director',
            'head of region' => 'regional_director',
            'regional head' => 'regional_director',
            'regional chief' => 'regional_director',
            'regional director' => 'regional_director',
            'rd' => 'regional_director',
            'incumbent regional_director' => 'current_regional_director',
            'current regional director' => 'current_regional_director',
            'current regional_director' => 'current_regional_director',
            'present regional director' => 'current_regional_director',
            'present regional_director' => 'current_regional_director',
            'kasalukuyang regional director' => 'current_regional_director',
            'kasalukuyang regional_director' => 'current_regional_director',
            'kasalukuyan regional director' => 'current_regional_director',
            'kasalukuyan regional_director' => 'current_regional_director',
            'ngayong regional director' => 'current_regional_director',
            'ngayong regional_director' => 'current_regional_director',
            'who heads' => 'who is',
            'who leads' => 'who is',
            'sino po' => 'sino',
            'sino ba' => 'sino',
        ];

        foreach ($phraseMap as $from => $to) {
            $t = preg_replace('/\b'.preg_quote($from, '/').'\b/u', $to, $t) ?? $t;
        }

        $t = preg_replace('/[^\p{L}\p{N}_\s]+/u', ' ', $t) ?? $t;
        $t = preg_replace('/\s+/u', ' ', $t) ?? $t;
        $tokenMap = [
            'actual' => 'current',
            'existing' => 'current',
            'incumbent' => 'current',
            'present' => 'current',
            'presently' => 'current',
            'currently' => 'current',
            'kasalukuyan' => 'current',
            'kasalukuyang' => 'current',
            'ngayon' => 'current',
            'ngayong' => 'current',
            'head' => 'director',
            'chief' => 'director',
            'lead' => 'director',
            'leads' => 'director',
            'sino' => 'who',
            'anu' => 'what',
            'ano' => 'what',
            'paano' => 'how',
            'kelan' => 'when',
            'kailan' => 'when',
            'saan' => 'where',
            'nasaan' => 'where',
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
