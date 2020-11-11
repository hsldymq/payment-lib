<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\NewebPayConfigInterface;

interface NewebPayConfigLoaderInterface
{
    public static function loadNewebPayConfig(array $context): NewebPayConfigInterface;
}