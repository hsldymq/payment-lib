<?php
namespace Utils\PaymentVendor\SignatureHelper\Weixin;

use Exception\UnsupportedVendorSignTypeException;
use Exception\VerifyVendorSignatureFailed;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Validator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WeixinConfig $config)
    {
        $this->config = $config;
    }

    public function verify(string $signature, string $sign_type, array $data, bool $throw_exception = false): bool
    {
        $sign_type = strtoupper($sign_type);
        $packed_string = $this->packRequestSignString($data);

        switch ($sign_type) {
            case 'MD5':
                $result = $this->verifyMD5($signature, $packed_string);
                break;
            case 'HMAC-SHA256':
                $result = $this->verifySHA256($signature, $packed_string);
                break;
            default:
                throw new UnsupportedVendorSignTypeException();
        }

        if (!$result && $throw_exception) {
            throw new VerifyVendorSignatureFailed("Failed To Verify Weixin Signature. Signauture To Be Verified: {$signature} Packed String: {$packed_string}");
        }

        return $result;
    }

    private function verifyMD5(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getSecretKey();

        return strtoupper(md5("{$packed_string}&key={$secret_key}")) === $signature;
    }

    private function verifySHA256(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getSecretKey();

        return strtoupper(hash_hmac('sha256', "{$packed_string}&key={$secret_key}", $secret_key)) === $signature;
    }
}