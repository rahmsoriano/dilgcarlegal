<?php

namespace App\Services;

use App\Exceptions\AiRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatClient
{
    public function chat(array $messages, ?string $model = null): array
    {
        $apiKey = config('services.groq.api_key');
        $baseUri = rtrim((string) config('services.groq.base_uri'), '/');
        $timeout = (int) config('services.groq.timeout', 60);
        $connectTimeout = (int) config('services.groq.connect_timeout', 10);
        $maxAttempts = (int) config('services.groq.max_attempts', 1);

        if (! is_string($apiKey) || $apiKey === '' || $apiKey === 'your_groq_api_key_here') {
            throw new AiRequestException('Groq API key is not configured.');
        }

        $payload = [
            'model' => $model ?: (string) config('services.groq.model', 'llama-3.3-70b-versatile'),
            'messages' => $messages,
        ];

        $attempt = 0;
        $backoffMs = 250;

        while (true) {
            $attempt++;

            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->asJson()
                    ->connectTimeout($connectTimeout)
                    ->timeout($timeout)
                    ->post($baseUri.'/chat/completions', $payload);
            } catch (ConnectionException $e) {
                if ($attempt < max(1, $maxAttempts)) {
                    usleep($backoffMs * 1000);
                    $backoffMs *= 2;

                    continue;
                }

                Log::warning('Groq connection error', ['message' => $e->getMessage()]);
                throw new AiRequestException('AI provider connection error (Groq).', null, 'connection_error');
            }

            if ($response->successful()) {
                $data = $response->json();
                $content = data_get($data, 'choices.0.message.content');

                if (! is_string($content)) {
                    throw new AiRequestException('AI provider returned an unexpected response (Groq).', $response->status(), 'unexpected_response', null, is_array($data) ? $data : null);
                }

                return [
                    'content' => $content,
                    'model' => data_get($data, 'model'),
                    'usage' => data_get($data, 'usage'),
                    'raw' => $data,
                ];
            }

            $status = $response->status();
            $errorPayload = $response->json();
            $error = is_array($errorPayload) ? (array) data_get($errorPayload, 'error', []) : [];
            $errorType = is_string(data_get($error, 'type')) ? (string) data_get($error, 'type') : null;
            $errorCode = is_string(data_get($error, 'code')) ? (string) data_get($error, 'code') : null;
            $errorMessage = is_string(data_get($error, 'message')) ? (string) data_get($error, 'message') : ('AI provider error (Groq HTTP '.$status.').');

            if (in_array($status, [429, 500, 502, 503, 504], true) && $attempt < max(1, $maxAttempts)) {
                $retryAfterSeconds = (int) ($response->header('Retry-After') ?: 0);
                $sleepMs = $retryAfterSeconds > 0 ? $retryAfterSeconds * 1000 : $backoffMs;
                usleep($sleepMs * 1000);
                $backoffMs *= 2;

                continue;
            }

            Log::warning('Groq request failed', [
                'http_status' => $status,
                'error_type' => $errorType,
                'error_code' => $errorCode,
            ]);

            throw new AiRequestException($errorMessage, $status, $errorType, $errorCode, is_array($errorPayload) ? $errorPayload : null);
        }
    }
}
