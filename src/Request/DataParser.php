<?php

namespace Archman\PaymentLib\Request;

class DataParser
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
                    // TODO throw correct exception
                    throw $e;
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
            // TODO throw correct exception
            throw new \Exception("Parse XML Document Failed: {$e->getMessage()}", $e->getCode(), $e);
        }

        return $data;
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