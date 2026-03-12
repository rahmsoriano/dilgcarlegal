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
        $model = $model ?: (string) config('services.gemini.model', 'gemini-1.5-flash');

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
        $maxAttempts = 3;
        $backoffMs = 250;

        while (true) {
            $attempt++;

            try {
                $response = Http::acceptJson()
                    ->asJson()
                    ->timeout($timeout)
                    ->withOptions(['query' => ['key' => $apiKey]])
                    ->post($baseUri.'/models/'.$model.':generateContent', $payload);
            } catch (ConnectionException $e) {
                if ($attempt < $maxAttempts) {
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
                    'model' => $model,
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

            if (in_array($status, [429, 500, 502, 503, 504], true) && $attempt < $maxAttempts) {
                usleep($backoffMs * 1000);
                $backoffMs *= 2;

                continue;
            }

            Log::warning('Gemini request failed', [
                'http_status' => $status,
                'error_type' => $errorType,
                'error_code' => $errorCode,
            ]);

            throw new AiRequestException($errorMessage, $status, $errorType, $errorCode, is_array($payloadErr) ? $payloadErr : null);
        }
    }
}
