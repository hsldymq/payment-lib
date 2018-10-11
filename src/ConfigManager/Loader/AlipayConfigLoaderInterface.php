<?php

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

/**
 * 支付宝配置载入器.
 */
interface AlipayConfigLoaderInterface extends ConfigLoaderInterface
{
    public static function load(array $context): AlipayConfigInterface;
}