<?php
namespace Archman\PaymentLib\Exception;

use Throwable;

class ErrorResponseException extends \Exception
{
    private $errorCode = null;

    private $errorText = null;

    public function __construct(?string $errorCode = null, ?string $errorText = null, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;

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
}