<?php

namespace App\Exceptions;

use RuntimeException;

class AiRequestException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly ?string $errorType = null,
        public readonly ?string $errorCode = null,
        public readonly ?array $errorPayload = null,
    ) {
        parent::__construct($message);
    }
}
