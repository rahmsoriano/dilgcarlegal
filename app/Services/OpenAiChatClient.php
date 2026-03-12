<?php

namespace App\Services;

use App\Exceptions\AiRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiChatClient
{
    public function chat(array $messages, ?string $model = null): array
    {
        $apiKey = config('services.openai.api_key');
        $baseUri = rtrim((string) config('services.openai.base_uri'), '/');
        $timeout = (int) config('services.openai.timeout', 60);

        if (! is_string($apiKey) || $apiKey === '') {
            throw new AiRequestException('OpenAI API key is not configured.');
        }

        $payload = [
            'model' => $model ?: (string) config('services.openai.model', 'gpt-4o-mini'),
            'messages' => $messages,
        ];

        $attempt = 0;
        $maxAttempts = 3;
        $backoffMs = 250;

        while (true) {
            $attempt++;

            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->asJson()
                    ->timeout($timeout)
                    ->post($baseUri.'/chat/completions', $payload);
            } catch (ConnectionException $e) {
                if ($attempt < $maxAttempts) {
                    usleep($backoffMs * 1000);
                    $backoffMs *= 2;

                    continue;
                }

                Log::warning('OpenAI connection error', ['message' => $e->getMessage()]);
                throw new AiRequestException('AI provider connection error.', null, 'connection_error');
            }

            if ($response->successful()) {
                $data = $response->json();
                $content = data_get($data, 'choices.0.message.content');

                if (! is_string($content)) {
                    throw new AiRequestException('AI provider returned an unexpected response.', $response->status(), 'unexpected_response', null, is_array($data) ? $data : null);
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
            $errorMessage = is_string(data_get($error, 'message')) ? (string) data_get($error, 'message') : ('AI provider error (HTTP '.$status.').');

            if (in_array($status, [429, 500, 502, 503, 504], true) && $attempt < $maxAttempts) {
                $retryAfterSeconds = (int) ($response->header('Retry-After') ?: 0);
                $sleepMs = $retryAfterSeconds > 0 ? $retryAfterSeconds * 1000 : $backoffMs;
                usleep($sleepMs * 1000);
                $backoffMs *= 2;

                continue;
            }

            Log::warning('OpenAI request failed', [
                'http_status' => $status,
                'error_type' => $errorType,
                'error_code' => $errorCode,
            ]);

            throw new AiRequestException($errorMessage, $status, $errorType, $errorCode, is_array($errorPayload) ? $errorPayload : null);
        }
    }
}
