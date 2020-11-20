<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

class DefaultClientFactoryProvider
{
    private static ClientFactoryInterface $factory;

    public static function getFactory(): ClientFactoryInterface
    {
        return self::$factory;
    }

    public static function setFactory(ClientFactoryInterface $factory): void
    {
        self::$factory = $factory;
    }
}

DefaultClientFactoryProvider::setFactory(new DefaultClientFactory());
