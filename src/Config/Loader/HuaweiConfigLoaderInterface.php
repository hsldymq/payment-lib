<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\HuaweiConfigInterface;

/**
 * 华为支付配置载入器.
 */
interface HuaweiConfigLoaderInterface
{
    public static function loadHuaweiConfig(array $context): HuaweiConfigInterface;
}