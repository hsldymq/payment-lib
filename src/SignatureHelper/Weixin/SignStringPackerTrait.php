<?php
namespace Utils\PaymentVendor\SignatureHelper\Weixin;

Trait SignStringPackerTrait
{
    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3 文档.
     * @param array $data
     * @return string
     */
    protected function packRequestSignString(array $data): string
    {
        unset($data['sign']);
        ksort($data);

        $concat_kv = [];
        foreach ($data as $k => $value) {
            if (!$this->isEmpty($value)) {
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