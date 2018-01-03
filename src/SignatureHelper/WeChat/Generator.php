<?php
namespace Archman\PaymentLib\SignatureHelper\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data, ?string $signType = null, array $exclude = []): string
    {
        $packed = $this->packRequestSignString($data, $exclude);

        $signType = $signType ?? $this->config->getDefaultSignType();

        switch (strtoupper($signType)) {
            case 'MD5':
                $sign = $this->makeSignMD5($packed);
                break;
            case 'HMAC-SHA256':
                $sign = $this->makeSignSHA256($packed);
                break;
            default:
                throw new SignatureException($data, "Unsupported WeChat Sign Type: {$signType}");
        }

        return $sign;
    }

    private function makeSignMD5(string $packedString): string
    {
        $secretKey = $this->config->getAPIKey();

        return strtoupper(md5("{$packedString}&key={$secretKey}"));
    }

    private function makeSignSHA256(string $packedString): string
    {
        $secretKey = $this->config->getAPIKey();

        return strtoupper(hash_hmac('sha256', "{$packedString}&key={$secretKey}", $secretKey));
    }
}