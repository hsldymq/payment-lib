<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Archman\PaymentLib\Exception\ContextualException;

final class DataConverterFactory
{
    /**
     * @var callable[]
     */
    private static array $converters = [];

    public static function registerConverter(string $name, callable $converter)
    {
        self::$converters[$name] = $converter;
    }

    public static function getConverter(string $name): callable
    {
        $cvt = self::$converters[$name] ?? null;
        if (!$cvt) {
            throw new ContextualException(['name' => $name], 'converter not registered');
        }

        return $cvt;
    }
}