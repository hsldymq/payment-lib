<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager\Loader;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;

interface MyCardConfigLoaderInterface
{
    public static function loadMyCardConfig(array $context): MyCardConfigInterface;
}