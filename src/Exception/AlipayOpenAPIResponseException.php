<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

/**
 * @method string getCode()
 */
class AlipayOpenAPIResponseException extends ContextualException
{
    public function getSubCode(): string
    {
        return $this->context['sub_code'] ?? '';
    }

    public function getSubMessage(): string
    {
        return $this->context['sub_msg'] ?? '';
    }
}