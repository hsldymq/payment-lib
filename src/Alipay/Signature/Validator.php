<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Signature;

use Archman\PaymentLib\Alipay\Config\MAPIConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Exception\SignException;

/**
 * 支付包签名验证器.
 */
class Validator
{
    private OpenAPIConfigInterface|MAPIConfigInterface $config;

    public function __construct(OpenAPIConfigInterface|MAPIConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * 验证API响应签名.
     *
     * @param string $signature
     * @param string $signType
     * @param string $data
     *
     * @return void
     * @throws
     */
    public function validateSign(string $signature, string $signType, string $data): void
    {
        $this->doValidateSign($signature, $signType, $data);
    }

    private function doValidateSign(string $signature, string $signType, string $packedString): bool
    {
        $signType = strtoupper($signType);
        switch ($signType) {
            case 'RSA':
                $result = $this->validateSignRSA($signature, $packedString);
                break;
            case 'RSA2':
                $result = $this->validateSignRSA2($signature, $packedString);
                break;
            case 'DSA':
                $result = $this->validateSignDSA($signature, $packedString);
                break;
            case 'MD5':
                $result = $this->validateSignMD5($signature, $packedString);
                break;
            default:
                throw (new SignException(['signType' => $signType], "unsupported sign type"));
        }

        if (!$result) {
            throw (new SignException(['signType' => $signType, 'signature' => $signature], "failed to validate signature"));
        }

        return true;
    }

    private function validateSignRSA(string $signature, string $packedString): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_get_publickey($pk);
        if (!$resource ||
            ($result = openssl_verify($packedString, base64_decode($signature), $resource)) === -1
        ) {
            throw new SignException(['signType' => 'RSA', 'alipayPublicKey' => $pk], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignRSA2(string $signature, string $packedString): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_get_publickey($pk);
        if (!$resource ||
            ($result = openssl_verify($packedString, base64_decode($signature), $resource, OPENSSL_ALGO_SHA256)) === -1
        ) {
            throw new SignException(['signType' => 'RSA2', 'alipayPublicKey' => $pk], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignDSA(string $signature, string $packedString): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_get_publickey($pk);
        if (!$resource ||
            ($result = openssl_verify($packedString, base64_decode($signature), $resource, OPENSSL_ALGO_DSS1)) === -1
        ) {
            throw new SignException(['signType' => 'DSA', 'alipayPublicKey' => $pk], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignMD5(string $signature, string $packedString): bool
    {
        $safeKey = $this->config->getPrivateKey('MD5');

        return md5("{$packedString}{$safeKey}") === $signature;
    }
}