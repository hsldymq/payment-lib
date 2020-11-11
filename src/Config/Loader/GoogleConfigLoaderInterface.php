<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\GoogleConfigInterface;

/**
 * 谷歌内购配置载入器.
 */
interface GoogleConfigLoaderInterface
{
    public static function loadGoogleConfig(array $context): GoogleConfigInterface;
}