<?php
namespace Archman\PaymentLib\SignatureHelper\Huawei;

trait SignStringPackerTrait
{
    /**
     * @link http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_sample_code_s
     * @param array $data
     * @return string
     */
    protected function packSignString(array $data): string
    {
        unset($data['sign']);
        ksort($data);

        $kv = [];
        foreach ($data as $k => $v) {
            if ($k !== "sign" && $k !== "signType") {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }
}