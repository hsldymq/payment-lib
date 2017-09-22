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

    public function __construct(AlipayConfigInterface $config)
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
    public function validateSignAsync(string $signature, string $sign_type, array $data, bool $throw_exception = false): bool
    {
        $packed_string = $this->packVerifiedSignStringAsync($data);

        return $this->validate($signature, $sign_type, $packed_string, $throw_exception);
    }

    /**
     * 验证同步返回的签名.
     * @param string $signature
     * @param string $sign_type
     * @param array $data
     * @param bool $throw_exception
     * @return bool
     */
    public function validateSignSync(string $signature, string $sign_type, array $data, bool $throw_exception = false): bool
    {
        $packed_string = $this->packVerifiedSignStringSync($data);

        return $this->validate($signature, $sign_type, $packed_string, $throw_exception);
    }

    private function validate(string $signature, string $sign_type, string $packed_string, bool $throw)
    {
        $sign_type = strtoupper($sign_type);
        switch ($sign_type) {
            case 'RSA':
                $result = $this->validateSignRSA($signature, $packed_string);
                break;
            case 'RSA2':
                $result = $this->validateSignRSA2($signature, $packed_string);
                break;
            case 'MD5':
                $result = $this->validateSignMD5($signature, $packed_string);
                break;
            default:
                // TODO
                throw new \Exception();
        }

        if (!$result && $throw) {
            // TODO
            throw new \Exception();
        }

        return $result;
    }

    private function validateSignRSA(string $signature, string $packed_string): bool
    {
        // TODO
        $key_resource = \openssl_get_publickey($this->config->getAlipayPublicKey('RSA'));
        if (!$key_resource) {
            // TODO
            throw new \Exception();
        }

        $is_correct = \openssl_verify($packed_string, base64_decode($signature), $key_resource) === 1;
        \openssl_free_key($key_resource);

        return $is_correct;
    }

    private function validateSignRSA2(string $signature, string $packed_string): bool
    {
        // TODO
        $key_resource = \openssl_get_publickey($this->config->getAlipayPublicKey('RSA2'));
        if (!$key_resource) {
            // TODO
            throw new \Exception();
        }

        $is_correct = \openssl_verify($packed_string, base64_decode($signature), $key_resource, OPENSSL_ALGO_SHA256) === 1;
        \openssl_free_key($key_resource);

        return $is_correct;
    }

    private function validateSignMD5(string $signature, string $packed_string): bool
    {
        $safe_key = $this->config->getMAPIPrivateKey();

        return md5("{$packed_string}{$safe_key}") === $signature;
    }
}