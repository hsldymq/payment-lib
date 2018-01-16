<?php
namespace Archman\PaymentLib\SignatureHelper\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * 支付包签名验证器.
 */
class Validator
{
    use SignStringPackerTrait;

    private $config;

    private $isMAPI;

    public function __construct(AlipayConfigInterface $config, bool $isMAPI = false)
    {
        $this->config = $config;
        $this->isMAPI = $isMAPI;
    }

    /**
     * 验证异步回调的签名.
     * @param string $signature 待验证的签名
     * @param string $signType 验证签名的算法(RSA, MD5, ...)
     * @param array $data 用于验证签名的数据
     * @param array $exclude
     * @return bool
     */
    public function validateSignAsync(string $signature, string $signType, array $data, array $exclude = []): bool
    {
        $packed = $this->packVerifiedSignStringAsync($data, $exclude);

        return $this->validate($signature, $signType, $packed, $data);
    }

    /**
     * 验证同步返回的签名.
     * @param string $signature
     * @param string $signType
     * @param array $data
     * @param array $exclude
     * @return bool
     */
    public function validateSignSync(string $signature, string $signType, array $data, array $exclude = []): bool
    {
        $packed = $this->packVerifiedSignStringSync($data, $exclude);

        return $this->validate($signature, $signType, $packed, $data);
    }

    private function validate(
        string $signature,
        string $signType,
        string $packedString,
        array $data
    ): bool {
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
                throw new SignatureException($data, "Unsupported Alipay Sign Type: {$signType}");
        }

        if (!$result) {
            throw new SignatureException($data, 'Failed To Validate Alipay Signature.');
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