<?php
namespace Archman\PaymentLib\SignatureHelper\Huawei;

trait SignStringPackerTrait
{
    /**
     * @link http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_sample_code_s
     * @param array $data
     * @param array $exclude
     * @return string
     */
    protected function packSignString(array $data, array $exclude = []): string
    {
        unset($data['sign']);
        ksort($data);

        $kv = [];
        foreach ($data as $k => $v) {
            if ($k !== "sign" &&
                $k !== "signType" &&
                $v !== null &&
                $v !== '' &&
                !in_array($k, $exclude)
            ) {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }
}