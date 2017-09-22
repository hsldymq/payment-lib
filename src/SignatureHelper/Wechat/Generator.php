<?php
namespace Archman\PaymentLib\SignatureHelper\Wechat;

use Archman\PaymentLib\ConfigManager\WechatConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;
use Archman\PaymentLib\SignatureHelper\SignAlgo;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WechatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data, string $sign_type, array $exclude = []): string
    {
        $packed_string = $this->packRequestSignString($data, $exclude);

        switch (strtoupper($sign_type)) {
            case 'MD5':
                $sign = $this->makeSignMD5($packed_string);
                break;
            case 'HMAC-SHA256':
                $sign = $this->makeSignSHA256($packed_string);
                break;
            default:
                throw new SignatureException("Unsupported Wechat Sign Type: {$sign_type}");
        }

        return $sign;
    }

    private function makeSignMD5(string $packed_string): string
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(md5("{$packed_string}&key={$secret_key}"));
    }

    private function makeSignSHA256(string $packed_string): string
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(hash_hmac('sha256', "{$packed_string}&key={$secret_key}", $secret_key));
    }
}