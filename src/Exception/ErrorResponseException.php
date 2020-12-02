<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class ErrorResponseException extends ContextualException
{
    private ?string $errorCode = null;

    private ?string $errorText = null;

    private ?array $responseData = null;

    public function __construct(
        ?string $errorCode = null,
        ?string $errorText = null,
        ?array $responseData = null,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct([
            'errorCode' => $errorCode,
            'errorText' => $errorText,
            'responseData' => $responseData,
        ], $message, $code, $previous);
    }

    public function getErrorCode(): ?string
    {
        return $this->context['errorCode'] ?? null;
    }

    public function getErrorText(): ?string
    {
        return $this->context['errorText'] ?? null;
    }

    public function getResponseData(): ?array
    {
        return $this->context['responseData'] ?? null;
    }
}