<?php
namespace Archman\PaymentLib\Request;

class DataParser
{
    public static function arrayToXML(array $data): string
    {
        // TODO
    }

    public static function xmlToArray(string $data): array
    {
        // TODO
        // TODO GBK编码使用simplexml库会报错
    }

    public static function formDataToArray(string $data): array
    {
        // TODO
    }

    public static function arrayToFormData(array $data): string
    {
        // TODO
    }

    public static function jsonToArray(string $data): array
    {
        return json_decode($data, true);
    }

    public static function arrayToJson(array $data): string
    {
        return json_encode($data, JSON_FORCE_OBJECT);
    }
}