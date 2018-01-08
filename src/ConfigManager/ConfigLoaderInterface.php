<?php
namespace Archman\PaymentLib\ConfigManager;

/**
 * 配置载入器.
 * 可以根据具体业务需要实现部分方法, 其他返回null.
 */
interface ConfigLoaderInterface
{
    public static function loadAlipayConfig(array $context): ?AlipayConfigInterface;

    public static function loadWeChatConfig(array $context): ?WeChatConfigInterface;

    public static function loadHuaweiConfig(array $context): ?HuaweiConfigInterface;

    public static function loadGoogleConfig(array $context): ?GoogleConfigInterface;
}