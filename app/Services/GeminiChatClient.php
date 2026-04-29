<?php

namespace App\Services;

use App\Exceptions\AiRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatClient
{
    public function chat(array $messages, ?string $model = null): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $baseUri = rtrim((string) config('services.gemini.base_uri'), '/');
        $timeout = (int) config('services.gemini.timeout', 60);
        $connectTimeout = (int) config('services.gemini.connect_timeout', 10);
        $maxAttempts = (int) config('services.gemini.max_attempts', 1);
        $maxModels = (int) config('services.gemini.max_models', 3);
        $model = $model ?: (string) config('services.gemini.model', 'gemini-1.5-flash');
        $verifySsl = (bool) config('services.gemini.verify_ssl', true);

        if ($apiKey === '') {
            throw new AiRequestException('Gemini API key is not configured.');
        }

        $systemInstructionText = null;
        $contents = [];

        foreach ($messages as $m) {
            $role = (string) ($m['role'] ?? '');
            $text = (string) ($m['content'] ?? '');
            if ($role === 'system') {
                $systemInstructionText = $text;

                continue;
            }
            $contents[] = [
                'role' => $role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $text]],
            ];
        }

        $payload = [
            'contents' => $contents,
        ];
        if ($systemInstructionText !== null && $systemInstructionText !== '') {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemInstructionText]],
            ];
        }

        $attempt = 0;
        $backoffMs = 250;

        $triedModels = [];
        $fallbackModels = [];
        if ($model !== '') {
            $fallbackModels[] = $model;
        }
        if ($model !== '' && ! str_ends_with($model, '-latest')) {
            $fallbackModels[] = $model.'-latest';
        }
        $fallbackModels[] = 'gemini-1.5-flash-latest';
        $fallbackModels[] = 'gemini-1.5-pro-latest';
        $fallbackModels[] = 'gemini-2.0-flash';

        if ($maxModels > 0) {
            $fallbackModels = array_slice($fallbackModels, 0, $maxModels);
        }

        $modelIndex = 0;

        while (true) {
            $attempt++;

            try {
                $options = ['query' => ['key' => $apiKey]];
                if (! $verifySsl) {
                    $options['verify'] = false;
                }

                $response = Http::acceptJson()
                    ->asJson()
                    ->connectTimeout($connectTimeout)
                    ->timeout($timeout)
                    ->withOptions($options)
                    ->post($baseUri.'/models/'.$fallbackModels[$modelIndex].':generateContent', $payload);
            } catch (ConnectionException $e) {
                if ($attempt < max(1, $maxAttempts)) {
                    usleep($backoffMs * 1000);
                    $backoffMs *= 2;

                    continue;
                }
                Log::warning('Gemini connection error', ['message' => $e->getMessage()]);
                throw new AiRequestException('AI provider connection error.', null, 'connection_error');
            }

            if ($response->successful()) {
                $data = $response->json();
                $text = (string) data_get($data, 'candidates.0.content.parts.0.text');
                if ($text === '') {
                    throw new AiRequestException('AI provider returned an unexpected response.', $response->status(), 'unexpected_response', null, is_array($data) ? $data : null);
                }

                return [
                    'content' => $text,
                    'model' => $fallbackModels[$modelIndex],
                    'usage' => [
                        'prompt_tokens' => data_get($data, 'usageMetadata.promptTokenCount'),
                        'completion_tokens' => data_get($data, 'usageMetadata.candidatesTokenCount'),
                        'total_tokens' => data_get($data, 'usageMetadata.totalTokenCount'),
                    ],
                    'raw' => $data,
                ];
            }

            $status = $response->status();
            $payloadErr = $response->json();
            $error = is_array($payloadErr) ? (array) data_get($payloadErr, 'error', []) : [];
            $errorType = is_string(data_get($error, 'status')) ? (string) data_get($error, 'status') : null;
            $errorCode = is_numeric(data_get($error, 'code')) ? (string) data_get($error, 'code') : null;
            $errorMessage = is_string(data_get($error, 'message')) ? (string) data_get($error, 'message') : ('AI provider error (HTTP '.$status.').');

            if ($status === 404) {
                $triedModels[] = $fallbackModels[$modelIndex];
                $modelIndex++;
                if ($modelIndex < count($fallbackModels)) {
                    $attempt = 0;
                    $backoffMs = 250;

                    continue;
                }
            }

            if (in_array($status, [500, 502, 503, 504], true) && $attempt < max(1, $maxAttempts)) {
                usleep($backoffMs * 1000);
                $backoffMs *= 2;

                continue;
            }

            Log::warning('Gemini request failed', [
                'http_status' => $status,
                'error_type' => $errorType,
                'error_code' => $errorCode,
                'model' => $fallbackModels[$modelIndex] ?? null,
                'tried_models' => $triedModels,
            ]);

            throw new AiRequestException($errorMessage, $status, $errorType, $errorCode, is_array($payloadErr) ? $payloadErr : null);
        }
    }
}
