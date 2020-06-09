<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\MyCard;

class HashHelper
{
    public static function makeHash(array $params): string
    {
        $encodedHashValue = self::makeEncodedHashValue($params);

        return hash('sha256', $encodedHashValue);
    }

    public static function makeEncodedHashValue(array $params): string
    {
        $preHashValue = self::makePreHashValue($params);

        $encoded = urlencode($preHashValue);
        return preg_replace_callback('/%[a-zA-Z0-9]{2}/', function ($each) {
            return strtolower($each[0]);
        }, $encoded);
    }

    public static function makePreHashValue(array $params): string
    {
        $v = '';
        foreach ($params as $key => $value) {
            $v .= $value;
        }

        return $v;
    }
}