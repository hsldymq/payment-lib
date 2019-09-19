<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

/**
 * 配置载入器.
 * 该接口由具体的接口继承,请实现同目录下的具体接口,不要实现这个接口
 */
interface ConfigLoaderInterface
{
    public static function load(array $context);
}