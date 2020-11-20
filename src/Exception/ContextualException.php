<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class ContextualException extends \Exception
{
    protected array $context;

    public function __construct(array $context, string $message = '', $code = 0, \Throwable $prev = null)
    {
        parent::__construct($message, intval($code), $prev);
        $this->code = $code;
        $this->context = $context;
    }

    final public function getContext(): array
    {
        return $this->context;
    }
}