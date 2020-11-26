<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Helper;

use Archman\PaymentLib\Exception\InvalidDataStructureException;

class OpenAPIHelper
{
    /**
     * 从JSON响应中提取response原始字符串.
     * 此方法用于在得到Open API响应时提取出response原始字符串进行验签.
     * 一个简单粗暴的提取方式是:
     *      先将body进行json_decode的到response的关联数组
     *      在json_encode还原会原始字符串
     * 但是这样做实际上是假设response json字符串是最小压缩模式,即字段间没有除了逗号没有其他分隔符(如空格等),一旦支付宝下发的不是这种模式,就会验签失败.
     * 所以这里直接通过substr进行提取.
     * 这里充分考虑到response字段有可能不是在整个json的固定位置,比如:
     *      response字段在sign字段之后
     *      将来的一些接口增加字段并放在了response字段之前
     * 所以这里动态的运算response字段的位置,以及它下一个字段的相对位置,通过计算差来得到response的实际内容
     *
     * @param string $data
     * @param string $fieldName response字段名
     *
     * @return string
     * @throws
     */
    public static function getResponseContent(string $data, string $fieldName): string
    {
        $parsed = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        preg_match("/\"{$fieldName}\"\s*:\s*/", $data, $matches);
        if (!isset($matches[0])) {
            throw new InvalidDataStructureException($data, ['fieldName' => $fieldName], 'expected specific field');
        }
        $contentFieldPos = strpos($data, $matches[0]);
        $contentFieldLen = strlen($matches[0]);

        preg_match("/\s*}\s*$/", $data, $matches);
        if (!isset($matches[0])) {
            throw new InvalidDataStructureException($data, [], "expected ending with '}'");
        }
        $length = -strlen($matches[0]);

        foreach ($parsed as $field => $value) {
            if ($field === $fieldName) {
                $isNext = true;
            } else if ($isNext ?? false) {
                preg_match("/\s*,\s*\"{$field}\"/", $data, $matches);
                if (!isset($matches[0])) {
                    throw new InvalidDataStructureException($data, ['fieldName' => $field], 'field order error');
                }
                $nextFieldPos = strpos($data, $matches[0]);
                $length = $nextFieldPos - $contentFieldPos - $contentFieldLen;
                break;
            }
        }

        return substr($data, $contentFieldPos + $contentFieldLen, $length);
    }
}