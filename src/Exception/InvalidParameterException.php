<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class InvalidParameterException extends \Exception
{
    private string $paramName;

    public function __construct(string $paramName, string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $this->paramName = $paramName;

        parent::__construct($message, $code, $previous);
    }

    public function getInvalidParamName(): string
    {
        return $this->paramName;
    }
}