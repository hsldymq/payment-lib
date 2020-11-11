<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\Alipay;

use Archman\PaymentLib\Config\AlipayConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * 支付包签名验证器.
 */
class Validator
{
    use SignStringPackerTrait;

    private AlipayConfigInterface $config;

    private bool $isMAPI;

    public function __construct(AlipayConfigInterface $config, bool $isMAPI = false)
    {
        $this->config = $config;
        $this->isMAPI = $isMAPI;
    }

    /**
     * 验证异步回调的签名.
     *
     * @param string $signature 待验证的签名
     * @param string $signType 验证签名的算法(RSA, MD5, ...)
     * @param array $data 用于验证签名的数据
     * @param array $exclude
     *
     * @return bool
     * @throws SignatureException
     */
    public function validateSignAsync(string $signature, string $signType, array $data, array $exclude = []): bool
    {
        $packed = $this->packVerifiedSignStringAsync($data, $exclude);

        try {
            return $this->validate($signature, $signType, $packed);
        } catch (\Throwable $e) {
            throw (new SignatureException($e->getMessage(), 0, $e))->setData($data)->setSign($signature);
        }
    }

    /**
     * 验证Open API同步响应的签名.
     *
     * @param string $signature
     * @param string $signType
     * @param string $data
     *
     * @return bool
     * @throws SignatureException
     */
    public function validateOpenAPIResponseSign(string $signature, string $signType, string $data): bool
    {
        try {
            return $this->validate($signature, $signType, $data);
        } catch (\Throwable $e) {
            throw (new SignatureException($e->getMessage(), 0, $e))->setData($data)->setSign($signature);
        }
    }

    private function validate(string $signature, string $signType, string $packedString): bool
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
                throw new \Exception("Unsupported Alipay Sign Type: {$signType}");
        }

        if (!$result) {
            throw new \Exception('Failed To Validate Alipay Signature.');
        }

        return true;
    }

    private function validateSignRSA(string $signature, string $packedString): bool
    {
        $resource = openssl_get_publickey($this->getAlipayPublicKey('RSA'));
        if (!$resource) {
            throw new \Exception("Unable To Get RSA Public Key");
        }

        $isCorrect = openssl_verify($packedString, base64_decode($signature), $resource) === 1;
        openssl_free_key($resource);

        return $isCorrect;
    }

    private function validateSignRSA2(string $signature, string $packedString): bool
    {
        $resource = openssl_get_publickey($this->getAlipayPublicKey('RSA2'));
        if (!$resource) {
            throw new \Exception("Unable To Get RSA2 Public Key");
        }

        $isCorrect = openssl_verify($packedString, base64_decode($signature), $resource, OPENSSL_ALGO_SHA256) === 1;
        openssl_free_key($resource);

        return $isCorrect;
    }

    private function validateSignDSA(string $signature, string $packedString): bool
    {
        $resource = openssl_get_publickey($this->getAlipayPublicKey('DSA'));
        if (!$resource) {
            throw new \Exception("Unable To Get DSA Public Key");
        }

        $isCorrect = openssl_verify($packedString, base64_decode($signature), $resource, OPENSSL_ALGO_DSS1) === 1;
        openssl_free_key($resource);

        return $isCorrect;
    }

    private function validateSignMD5(string $signature, string $packedString): bool
    {
        $safeKey = $this->getAlipayPublicKey('MD5');

        return md5("{$packedString}{$safeKey}") === $signature;
    }

    private function getAlipayPublicKey(string $signType): string
    {
        if ($this->isMAPI) {
            return $this->config->getMAPIAlipayPublicKey($signType);
        } else {
            return $this->config->getOpenAPIAlipayPublicKey($signType);
        }
    }
}