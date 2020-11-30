<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

/**
 * 验证签名时产生的异常.
 *
 * 通过 getContext 方法获取上下文信息.
 */
class SignValidationException extends ContextualException
{
}