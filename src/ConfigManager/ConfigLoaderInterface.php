<?php
namespace Archman\PaymentLib\ConfigManager;

interface ConfigLoaderInterface
{
    public static function loadAlipayConfig(array $context): AlipayConfigInterface;

    public static function loadWeChatConfig(array $context): WeChatConfigInterface;

    public static function loadHuaweiConfig(array $context): HuaweiConfigInterface;

    public static function loadGoogleConfig(array $context): GoogleConfigInterface;
}