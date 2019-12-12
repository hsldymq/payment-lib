<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request;

use Archman\PaymentLib\Exception\InternalErrorException;

class DataConverter
{
    public static function arrayToXML(array $data, string $root = 'xml'): string
    {
        $xml     = $root ? "<{$root}>%s</{$root}>" : '%s';
        $content = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // 允许嵌套
                $content .= "<{$key}>".self::arrayToXML($value, '')."</{$key}>";
            } else {
                try {
                    $content .= "<{$key}><![CDATA[".strval($value)."]]></{$key}>";
                } catch (\Throwable $e) {
                    throw new InternalErrorException([
                        'data' => $data,
                    ], "array to xml: {$e->getMessage()}", 0, $e);
                }
            }
        }

        return sprintf($xml, $content);
    }

    public static function xmlToArray(string $xml): array
    {
        $parse_function = function (\SimpleXMLElement $element) use (&$parse_function) {
            $data = [];
            /** @var \SimpleXMLElement $value */
            foreach ($element as $key => $value) {
                if ($child = $value->children()) {
                    $data[$key] = $parse_function($child);
                } else {
                    $data[$key] = (string) $value;
                }
            }

            return $data;
        };
        try {
            $document = simplexml_load_string($xml);
            $data     = $parse_function($document);
        } catch (\Throwable $e) {
            throw new InternalErrorException(['xml' => $xml], "xml to array: {$e->getMessage()}", 0, $e);
        }

        return $data;
    }

    public static function jsonToArray(string $data): array
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    public static function arrayToJson(array $data): string
    {
        return json_encode($data, JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
    }
}