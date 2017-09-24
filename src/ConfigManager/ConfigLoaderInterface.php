<?php
namespace Archman\PaymentLib\ConfigManager;

interface ConfigLoaderInterface
{
    public static function loadAlipayConfig(array $context): AlipayConfigInterface;

    public static function loadWeChatConfig(array $context): WeChatConfigInterface;
}