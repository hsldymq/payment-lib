<?php
namespace Archman\PaymentLib\SignatureHelper\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

class Generator
{
    use SignStringPackerTrait;

    private $config;

    private $isMAPI;

    public function __construct(AlipayConfigInterface $config, bool $isMAPI = false)
    {
        $this->config = $config;
        $this->isMAPI = $isMAPI;
    }

    public function makeSign(array $data, ?string $signType = null, array $exclude = []): string
    {
        $signType = $signType ?? ($this->isMAPI ? $this->config->getMAPIDefaultSignType() : $this->config->getOpenAPIDefaultSignType());
        $packed = $this->packRequestSignString($data, $exclude);

        switch (strtoupper($signType)) {
            case 'RSA':
                $sign = $this->makeSignRSA($packed);
                break;
            case 'RSA2':
                $sign = $this->makeSignRSA2($packed);
                break;
            case 'MD5':
                $sign = $this->makeSignMD5($packed);
                break;
            default:
                throw new SignatureException($data, "Unsupported Alipay Sign Type: {$signType}");
        }

        return $sign;
    }

    private function makeSignRSA(string $packedString): string
    {
        $pk = $this->getPrivateKey('RSA');
        $resource = openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get RSA Private Key");
        }

        openssl_sign($packedString, $sign, $resource);
        openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignRSA2(string $packedString): string
    {
        $pk = $this->getPrivateKey('RSA2');
        $resource = openssl_get_privatekey($pk);
        if (!$resource) {
            throw new \Exception("Unable To Get RSA2 Private Key");
        }

        openssl_sign($packedString, $sign, $resource, OPENSSL_ALGO_SHA256);
        openssl_free_key($resource);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignMD5(string $packedString): string
    {
        $safeKey = $this->getPrivateKey('MD5');

        return md5("{$packedString}{$safeKey}");
    }

    private function getPrivateKey(string $algo): string
    {
        $pk = $this->isMAPI ? $this->config->getMAPIPrivateKey($algo) : $this->config->getOpenAPIPrivateKey($algo);

        return $pk;
    }
}