<?php
namespace Archman\PaymentLib\SignatureHelper\Weixin;

use Archman\PaymentLib\ConfigManager\WeixinConfigInterface;
use Exception\UnsupportedVendorSignTypeException;
use Exception\VerifyVendorSignatureFailed;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Validator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WeixinConfigInterface $config)
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
                // TODO
                throw new \Exception();
        }

        if (!$result && $throw_exception) {
            // TODO
            throw new \Exception();
        }

        return $result;
    }

    private function verifyMD5(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(md5("{$packed_string}&key={$secret_key}")) === $signature;
    }

    private function verifySHA256(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(hash_hmac('sha256', "{$packed_string}&key={$secret_key}", $secret_key)) === $signature;
    }
}