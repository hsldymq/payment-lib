<?php

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;

interface MyCardConfigLoaderInterface extends ConfigLoaderInterface
{
    public static function load(array $context): MyCardConfigInterface;
}