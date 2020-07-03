<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\NewebPayConfigInterface;

interface NewebPayConfigLoaderInterface
{
    public static function loadNewebPayConfig(array $context): NewebPayConfigInterface;
}