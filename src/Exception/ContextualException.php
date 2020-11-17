<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class ContextualException extends \Exception
{
    protected array $context;

    public function __construct(array $context, string $message = '', int $code = 0, \Throwable $prev = null)
    {
        parent::__construct($message, $code, $prev);
        $this->context = $context;
    }

    final public function getContext(): array
    {
        return $this->context;
    }
}