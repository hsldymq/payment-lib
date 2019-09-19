<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;

/**
 * 微信支付配置载入器.
 */
interface WeChatConfigLoaderInterface extends ConfigLoaderInterface
{
    public static function load(array $context): WeChatConfigInterface;
}