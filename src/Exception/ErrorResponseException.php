<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class ErrorResponseException extends \Exception
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
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;
        $this->responseData = $responseData;

        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorText(): ?string
    {
        return $this->errorText;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}