<?php
namespace Utils\PaymentVendor\SignatureHelper\Alipay;

use Exception\InvalidPaymentPublicKeyException;
use Exception\UnsupportedVendorSignTypeException;
use Exception\VerifyVendorSignatureFailed;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;

/**
 * 支付包签名验证器.
 */
class Validator
{
    use SignStringPackerTrait;

    /** @var AlipayConfig */
    private $config;

    public function __construct(AlipayConfig $config)
    {
        $this->config = $config;
    }

    /**
     * 验证异步回调的签名.
     * @param string $signature 待验证的签名
     * @param string $sign_type 验证签名的算法(RSA, MD5, ...)
     * @param array $data 用于验证签名的数据
     * @param bool $throw_exception 如果验证失败是否抛出异常,这个只是在验证阶段发现签名不一致的情况下指示是否抛出异常. 如果出现其他错误是必然抛出异常.
     * @return bool
     */
    public function verifyAsync(string $signature, string $sign_type, array $data, bool $throw_exception = false): bool
    {
        $packed_string = $this->packVerifiedSignStringAsync($data);

        return $this->verify($signature, $sign_type, $packed_string, $throw_exception);
    }

    /**
     * 验证同步返回的签名.
     * @param string $signature
     * @param string $sign_type
     * @param array $data
     * @param bool $throw_exception
     * @return bool
     */
    public function verifySync(string $signature, string $sign_type, array $data, bool $throw_exception = false): bool
    {
        $packed_string = $this->packVerifiedSignStringSync($data);

        return $this->verify($signature, $sign_type, $packed_string, $throw_exception);
    }

    private function verify(string $signature, string $sign_type, string $packed_string, bool $throw)
    {
        $sign_type = strtoupper($sign_type);
        switch ($sign_type) {
            case 'RSA':
                $result = $this->verifyRSA($signature, $packed_string);
                break;
            case 'RSA2':
                $result = $this->verifyRSA2($signature, $packed_string);
                break;
            case 'MD5':
                $result = $this->verifyMD5($signature, $packed_string);
                break;
            default:
                throw new UnsupportedVendorSignTypeException("Unsupported Alipay Sign Type: {$sign_type}");
        }

        if (!$result && $throw) {
            throw new VerifyVendorSignatureFailed("Failed To Verify Alipay Signature. Signauture To Be Verified: {$signature} Packed String: {$packed_string}");
        }

        return $result;
    }

    private function verifyRSA(string $signature, string $packed_string): bool
    {
        $key_resource = \openssl_get_publickey($this->config->getAlipayPublicKeyRSA());
        if (!$key_resource) {
            throw new InvalidPaymentPublicKeyException('Invalid Alipay Public Key');
        }

        $is_correct = \openssl_verify($packed_string, base64_decode($signature), $key_resource) === 1;
        \openssl_free_key($key_resource);

        return $is_correct;
    }

    private function verifyRSA2(string $signature, string $packed_string): bool
    {
        $key_resource = \openssl_get_publickey($this->config->getAlipayPublicKeyRSA2());
        if (!$key_resource) {
            throw new InvalidPaymentPublicKeyException('Invalid Alipay Public Key');
        }

        $is_correct = \openssl_verify($packed_string, base64_decode($signature), $key_resource, OPENSSL_ALGO_SHA256) === 1;
        \openssl_free_key($key_resource);

        return $is_correct;
    }

    private function verifyMD5(string $signature, string $packed_string): bool
    {
        $safe_key = $this->config->getSafeKey();

        return md5("{$packed_string}{$safe_key}") === $signature;
    }
}