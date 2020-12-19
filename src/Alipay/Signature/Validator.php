<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Signature;

use Archman\PaymentLib\Alipay\Config\MAPIConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Exception\ContextualException;
use Archman\PaymentLib\Exception\SignValidationException;

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

    private function doValidateSign(string $signature, string $signType, string $data): bool
    {
        $signType = strtoupper($signType);
        switch ($signType) {
            case 'RSA':
                $result = $this->validateSignRSA($signature, $data);
                break;
            case 'RSA2':
                $result = $this->validateSignRSA2($signature, $data);
                break;
            case 'DSA':
                $result = $this->validateSignDSA($signature, $data);
                break;
            case 'MD5':
                $result = $this->validateSignMD5($signature, $data);
                break;
            default:
                throw (new SignValidationException(['signType' => $signType], "unsupported sign type"));
        }

        if (!$result) {
            throw (new SignValidationException(['signType' => $signType, 'signature' => $signature, 'data' => $data], "failed to validate signature"));
        }

        return true;
    }

    private function validateSignRSA(string $signature, string $data): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_pkey_get_public(self::tryGetPKContent($pk));
        if (!$resource ||
            ($result = openssl_verify($data, base64_decode($signature), $resource)) === -1
        ) {
            throw new SignValidationException(['signType' => 'RSA', 'signature' => $signature, 'data' => $data], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignRSA2(string $signature, string $data): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_pkey_get_public(self::tryGetPKContent($pk));
        if (!$resource ||
            ($result = openssl_verify($data, base64_decode($signature), $resource, OPENSSL_ALGO_SHA256)) === -1
        ) {
            throw new SignValidationException(['signType' => 'RSA2', 'signature' => $signature, 'data' => $data], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignDSA(string $signature, string $data): bool
    {
        $pk = $this->config->getAlipayPublicKey();
        $resource = openssl_pkey_get_public(self::tryGetPKContent($pk));
        if (!$resource ||
            ($result = openssl_verify($data, base64_decode($signature), $resource, OPENSSL_ALGO_DSS1)) === -1
        ) {
            throw new SignValidationException(['signType' => 'DSA', 'signature' => $signature, 'data' => $data], openssl_error_string());
        }

        return $result === 1;
    }

    private function validateSignMD5(string $signature, string $packedString): bool
    {
        $safeKey = $this->config->getPrivateKey('MD5');

        return md5("{$packedString}{$safeKey}") === $signature;
    }

    private static function tryGetPKContent(string $pathOrContent): string
    {
        if (!is_file($pathOrContent)) {
            return $pathOrContent;
        }

        $content = file_get_contents($pathOrContent);
        if ($content === false) {
            throw new ContextualException([], error_get_last()['message']);
        }
        return $content;
    }
}