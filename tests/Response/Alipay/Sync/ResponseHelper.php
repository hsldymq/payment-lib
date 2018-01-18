<?php
namespace Archman\PaymentLib\Test\Response\Alipay\Sync;

class ResponseHelper
{
    public static function getResponseFieldName(string $class): array
    {
        return (function () {
            return [
                'signName' => self::SIGN_FIELD,
                'responseName' => self::CONTENT_FIELD
            ];
        })->bindTo(null, $class)();
    }
}