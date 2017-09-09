<?php
namespace Archman\PaymentLib\ConfigManager;

interface ConfigLoaderInterface
{
    public static function loadAlipayConfig(array $context): AlipayConfigInterface;

    public static function loadWeixinConfig(array $context): WeixinConfigInterface;
}