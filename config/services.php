<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_uri' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 25),
        'connect_timeout' => (int) env('OPENAI_CONNECT_TIMEOUT', 10),
        'max_attempts' => (int) env('OPENAI_MAX_ATTEMPTS', 1),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', env('gemini_api_key')),
        'base_uri' => env('GEMINI_BASE_URL', env('gemini_base_url', 'https://generativelanguage.googleapis.com/v1beta')),
        'model' => env('GEMINI_MODEL', env('gemini_model', 'gemini-1.5-flash')),
        'timeout' => (int) env('GEMINI_TIMEOUT', (int) env('gemini_timeout', 25)),
        'connect_timeout' => (int) env('GEMINI_CONNECT_TIMEOUT', 10),
        'max_attempts' => (int) env('GEMINI_MAX_ATTEMPTS', 1),
        'max_models' => (int) env('GEMINI_MAX_MODELS', 3),
        'verify_ssl' => (bool) env('GEMINI_VERIFY_SSL', env('APP_ENV') === 'local' ? false : true),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'base_uri' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'timeout' => (int) env('GROQ_TIMEOUT', 25),
        'connect_timeout' => (int) env('GROQ_CONNECT_TIMEOUT', 10),
        'max_attempts' => (int) env('GROQ_MAX_ATTEMPTS', 1),
    ],

    'chat' => [
        'system_prompt' => env('CHAT_SYSTEM_PROMPT', ''),
    ],

];
