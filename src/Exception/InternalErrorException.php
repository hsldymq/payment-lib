<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

use Throwable;

class InternalErrorException extends \Exception
{
    private array $context = [];

    public function __construct(array $context, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}