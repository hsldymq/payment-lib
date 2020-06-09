<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

class Generator
{
    use SignStringPackerTrait;

    private AlipayConfigInterface $config;

    private bool $isMAPI;

    public function __construct(AlipayConfigInterface $config, bool $isMAPI = false)
    {
        $this->config = $config;
        $this->isMAPI = $isMAPI;
    }

    public function makeSign(array $data, ?string $signType = null, array $exclude = []): string
    {
        $signType = $signType ?? ($this->isMAPI ? $this->config->getMAPIDefaultSignType() : $this->config->getOpenAPIDefaultSignType());
        $packed = $this->packRequestSignString($data, $exclude);

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
                throw (new SignatureException("Unsupported Alipay Sign Type: {$signType}"))->setData($data);
        }

        return $sign;
    }

    private function makeSignRSA(string $packedString): string
    {
        $pk = $this->getPrivateKey('RSA');
        $resource = openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get RSA Private Key");
        }

        openssl_sign($packedString, $sign, $resource);
        openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignRSA2(string $packedString): string
    {
        $pk = $this->getPrivateKey('RSA2');
        $resource = openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get RSA2 Private Key");
        }

        openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_SHA256);
        openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignDSA(string $packedString): string
    {
        $pk = $this->getPrivateKey('DSA');
        $resource = openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get DSA Private Key");
        }

        openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_DSS1);
        openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignMD5(string $packedString): string
    {
        $safeKey = $this->getPrivateKey('MD5');

        return md5("{$packedString}{$safeKey}");
    }

    private function getPrivateKey(string $signType): string
    {
        if ($this->isMAPI) {
            return $this->config->getMAPIPrivateKey($signType);
        } else {
            return $this->config->getOpenAPIPrivateKey($signType);
        }
    }
}