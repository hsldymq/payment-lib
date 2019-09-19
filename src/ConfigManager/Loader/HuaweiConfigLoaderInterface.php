<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;

/**
 * 华为支付配置载入器.
 */
interface HuaweiConfigLoaderInterface extends ConfigLoaderInterface
{
    public static function load(array $context): HuaweiConfigInterface;
}