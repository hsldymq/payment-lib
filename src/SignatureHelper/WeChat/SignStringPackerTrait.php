<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\WeChat;

Trait SignStringPackerTrait
{
    /**
     * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3 生成待签名字符串算法
     *
     * @param array $data
     * @param array $exclude 不参与签名的参数名列表
     *
     * @return string
     */
    protected function packRequestSignString(array $data, array $exclude = []): string
    {
        unset($data['sign']);
        ksort($data);

        $kv = [];
        foreach ($data as $k => $v) {
            if (!$this->isEmpty($v) && !in_array($k, $exclude)) {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }

    private function isEmpty($value): bool
    {
        return !isset($value) || $value === null || (is_string($value) && trim($value) === '');
    }
}