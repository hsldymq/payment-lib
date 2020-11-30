<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

/**
 * 当支付宝OpenAPI响应数据中code不为10000时抛此异常.
 *
 * @method string getCode() 获取响应中code值
 * @method string getMessage() 获取响应中msg值
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