<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient
{
    public function generate(string $userText, string $systemInstruction = '', array $options = []): string
    {
        $apiKey = (string) config('services.gemini.api_key');
        $baseUri = rtrim((string) config('services.gemini.base_uri'), '/');
        $model = (string) config('services.gemini.model');
        $timeout = (int) config('services.gemini.timeout', 60);

        if ($apiKey === '' || $model === '' || $baseUri === '') {
            throw new RuntimeException('Gemini is not configured.');
        }

        $endpointBase = $baseUri;
        if (str_ends_with($endpointBase, '/models')) {
            $endpointBase = substr($endpointBase, 0, -strlen('/models'));
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userText],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.6,
                'maxOutputTokens' => $options['max_output_tokens'] ?? 1200,
            ],
        ];

        if (trim($systemInstruction) !== '') {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        $url = $endpointBase.'/models/'.$model.':generateContent';

        try {
            $resp = Http::timeout($timeout)
                ->acceptJson()
                ->asJson()
                ->post($url.'?key='.$apiKey, $payload);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Gemini request failed.', 0, $e);
        }

        if (! $resp->ok()) {
            $message = $resp->json('error.message');
            if (! is_string($message) || trim($message) === '') {
                $message = $resp->body();
            }
            $message = is_string($message) ? trim($message) : '';
            $hint = $resp->status() === 404
                ? ' Check GEMINI_BASE_URL and GEMINI_MODEL.'
                : '';

            throw new RuntimeException('Gemini request failed with status '.$resp->status().'.'.($message !== '' ? ' '.$message : '').$hint);
        }

        $data = $resp->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $text;
    }
}
