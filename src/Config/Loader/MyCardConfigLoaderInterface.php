<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config\Loader;

use Archman\PaymentLib\Config\MyCardConfigInterface;

interface MyCardConfigLoaderInterface
{
    public static function loadMyCardConfig(array $context): MyCardConfigInterface;
}