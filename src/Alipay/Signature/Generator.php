<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Signature;

use Archman\PaymentLib\Alipay\Config\MAPIConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Exception\SignException;

class Generator
{
    private OpenAPIConfigInterface|MAPIConfigInterface $config;

    public function __construct(OpenAPIConfigInterface|MAPIConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data, array $exclude = []): string
    {
        $packed = $this->packRequestSignString($data, $exclude);

        $signType = $this->config->getSignType();
        switch (strtoupper($signType)) {
            case 'RSA':
                $sign = $this->makeSignRSA($packed);
                break;
            case 'RSA2':
                $sign = $this->makeSignRSA2($packed);
                break;
            case 'DSA':
                $sign = $this->makeSignDSA($packed);
                break;
            case 'MD5':
                $sign = $this->makeSignMD5($packed);
                break;
            default:
                throw (new SignException(['signType' => $signType], "unsupported sign type"));
        }

        return $sign;
    }

    private function makeSignRSA(string $packedString): string
    {
        $pk = $this->config->getPrivateKey();
        $resource = openssl_get_privatekey($pk);
        if (!$resource || !openssl_sign($packedString, $sign, $resource)) {
            throw new SignException(['signType' => 'RSA', 'privateKey' => $pk], openssl_error_string());
        }
        openssl_free_key($resource);

        return base64_encode($sign);
    }

    private function makeSignRSA2(string $packedString): string
    {
        $pk = $this->config->getPrivateKey();

        $resource = openssl_get_privatekey($pk);
        if (!$resource || !openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_SHA256)) {
            throw new SignException(['signType' => 'RSA2', 'privateKey' => $pk], openssl_error_string());
        }
        openssl_free_key($resource);

        return base64_encode($sign);
    }

    private function makeSignDSA(string $packedString): string
    {
        $pk = $this->config->getPrivateKey();
        $resource = openssl_get_privatekey($pk);
        if (!$resource || !openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_DSS1)) {
            throw new SignException(['signType' => 'DSA', 'privateKey' => $pk], openssl_error_string());
        }
        openssl_free_key($resource);

        return base64_encode($sign);
    }

    private function makeSignMD5(string $packedString): string
    {
        $safeKey = $this->config->getPrivateKey();

        return md5("{$packedString}{$safeKey}");
    }

    private function packRequestSignString(array $data, array $exclude): string
    {
        ksort($data);
        $kv = [];
        foreach ($data as $k => $v) {
            if (!$this->isEmpty($v) &&
                !$this->isSignField($k) &&
                !$this->startWithAt($v) &&
                !in_array($k, $exclude)
            ) {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }

    private function isEmpty($value): bool
    {
        return !isset($value) || $value === null || (is_string($value) && trim($value) === '');
    }

    private function startWithAt($value): bool
    {
        return is_string($value) && $value[0] === '@';
    }

    private function isSignField(string $field_name): bool
    {
        return $field_name === 'sign';
    }
}