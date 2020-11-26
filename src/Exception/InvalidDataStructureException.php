<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class InvalidDataStructureException extends ContextualException
{
    public function __construct(string $rawData, array $context = [], string $message = '', $code = 0, \Throwable $prev = null)
    {
        parent::__construct(array_merge($context, ['rawData' => $rawData]), $message, $code, $prev);
    }

    public function getRawData(): string
    {
        return $this->context['rawData'];
    }
}