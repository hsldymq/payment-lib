<?php
namespace Archman\PaymentLib\SignatureHelper\Huawei;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;

/**
 * @link http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_sample_code_s
 */
class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(HuaweiConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data): string
    {
        $packed = $this->packSignString($data);
        $sign = $this->makeSignRSA256($packed);

        return $sign;
    }

    private function makeSignRSA256(string $packedString): string
    {
        $pk = $this->config->getPrivateKey();
        $resource = \openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get RSA Private Key");
        }

        \openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_SHA256);
        \openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }
}
