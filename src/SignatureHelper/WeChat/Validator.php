<?php
namespace Archman\PaymentLib\SignatureHelper\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Validator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $signature
     * @param string $signType
     * @param array $data
     * @param array $exclude
     * @throws SignatureException
     */
    public function validate(string $signature, string $signType, array $data, array $exclude = [])
    {
        $packed = $this->packRequestSignString($data, $exclude);

        switch (strtoupper($signType)) {
            case 'MD5':
                $result = $this->validateSignMD5($signature, $packed);
                break;
            case 'HMAC-SHA256':
                $result = $this->validateSignSHA256($signature, $packed);
                break;
            default:
                throw (new SignatureException("Unsupported WeChat Sign Type: {$signType}"))->setData($data)->setSign($signature);
        }

        if (!$result) {
            throw (new SignatureException('Failed To Validate WeChat Signature.'))->setData($data)->setSign($signature);
        }
    }

    private function validateSignMD5(string $signature, string $packedString): bool
    {
        $secretKey = $this->config->getAPIKey();

        return strtoupper(md5("{$packedString}&key={$secretKey}")) === $signature;
    }

    private function validateSignSHA256(string $signature, string $packedString): bool
    {
        $secretKey = $this->config->getAPIKey();

        return strtoupper(hash_hmac('sha256', "{$packedString}&key={$secretKey}", $secretKey)) === $signature;
    }
}