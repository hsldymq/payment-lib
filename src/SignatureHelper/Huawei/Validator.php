<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\Huawei;

use Archman\PaymentLib\Config\HuaweiConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * @see http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_api_reference_c5
 * @see http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_sample_code_s
 */
class Validator
{
    use SignStringPackerTrait;

    private HuaweiConfigInterface $config;

    public function __construct(HuaweiConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $signature
     * @param string $signType
     * @param array $data
     *
     * @return bool
     * @throws SignatureException
     */
    public function validate(string $signature, string $signType, array $data): bool
    {
        $packed = $this->packSignString($data);

        switch (strtoupper($signType)) {
            case 'RSA':
                $result = $this->validateSignRSA($signature, $packed);
                break;
            case 'RSA256':
                $result = $this->validateSignRSA($signature, $packed, true);
                break;
            default:
                throw (new SignatureException("Unsupported WeChat Sign Type: {$signType}"))->setData($data)->setSign($signature);
        }

        if (!$result) {
            throw (new SignatureException('Failed To Validate WeChat Signature.'))->setData($data)->setSign($signature);
        }

        return true;
    }

    private function validateSignRSA(string $signature, string $packedString, bool $sha256 = false): bool
    {
        $resource = openssl_get_publickey($this->config->getPublicKey());
        if (!$resource) {
            throw new \Exception("Unable To Get Public Key");
        }

        $params = [$packedString, base64_decode($signature), $resource];
        $sha256 && $params[] = OPENSSL_ALGO_SHA256;
        $isCorrect = openssl_verify(...$params) === 1;
        openssl_free_key($resource);

        return $isCorrect;
    }
}