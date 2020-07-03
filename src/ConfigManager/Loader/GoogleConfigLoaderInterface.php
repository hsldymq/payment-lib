<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\GoogleConfigInterface;

/**
 * 谷歌内购配置载入器.
 */
interface GoogleConfigLoaderInterface
{
    public static function load(array $context): GoogleConfigInterface;
}