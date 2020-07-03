<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\NewebPayConfigInterface;

interface NewebPayConfigLoaderInterface extends ConfigLoaderInterface
{
    public static function load(array $context): NewebPayConfigInterface;
}