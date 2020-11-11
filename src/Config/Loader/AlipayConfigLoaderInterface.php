<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\AlipayConfigInterface;

/**
 * 支付宝配置载入器.
 */
interface AlipayConfigLoaderInterface
{
    public static function loadAlipayConfig(array $context): AlipayConfigInterface;
}