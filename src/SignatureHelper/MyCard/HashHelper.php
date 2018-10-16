<?php

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
        $v = '';
        foreach ($params as $key => $value) {
            $v .= $value;
        }

        $encoded = rawurlencode($v);

        return preg_replace_callback('/%[a-zA-Z0-9]{2}/', function ($each) {
            return strtolower($each[0]);
        }, $encoded);
    }
}