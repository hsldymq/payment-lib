<?php
namespace Archman\PaymentLib\SignatureHelper\WeChat;

Trait SignStringPackerTrait
{
    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
     * @param array $data
     * @param array $exclude
     * @return string
     */
    protected function packRequestSignString(array $data, array $exclude = []): string
    {
        unset($data['sign']);
        ksort($data);

        $concat_kv = [];
        foreach ($data as $k => $value) {
            if (!$this->isEmpty($value) || in_array($k, $exclude)) {
                $concat_kv[] = "{$k}={$value}";
            }
        }

        return implode('&', $concat_kv);
    }

    private function isEmpty($value): bool
    {
        return !isset($value) || $value === null || (is_string($value) && trim($value) === '');
    }
}