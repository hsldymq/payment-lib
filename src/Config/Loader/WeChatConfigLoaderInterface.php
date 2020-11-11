<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\WeChatConfigInterface;

/**
 * 微信支付配置载入器.
 */
interface WeChatConfigLoaderInterface
{
    public static function loadWeChatConfig(array $context): WeChatConfigInterface;
}