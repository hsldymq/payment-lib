<?php
namespace Archman\PaymentLib\SignatureHelper\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data, string $sign_type): string
    {
        $sign_type = strtoupper($sign_type);
        $packed_string = $this->packRequestSignString($data);

        switch ($sign_type) {
            case 'RSA':
                $sign = $this->makeSignRSA($packed_string);
                break;
            case 'RSA2':
                $sign = $this->makeSignRSA2($packed_string);
                break;
            case 'MD5':
                $sign = $this->makeSignMD5($packed_string);
                break;
            default:
                // TODO
                throw new \Exception();
        }

        return $sign;
    }

    private function makeSignRSA(string $packed_string): string
    {
        // TODO
        $res = \openssl_get_privatekey($this->config->getAppPrivateKey('RSA'));
        if (!$res) {
            // TODO
            throw new \Exception();
        }

        \openssl_sign($packed_string, $sign, $res);
        \openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignRSA2(string $packed_string): string
    {
        // TODO
        $res = \openssl_get_privatekey($this->config->getAppPrivateKey('RSA2'));
        if (!$res) {
            // TODO
            throw new \Exception();
        }

        \openssl_sign($packed_string, $sign, $res, OPENSSL_ALGO_SHA256);
        \openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignMD5(string $packed_string): string
    {
        $safe_key = $this->config->getSafeKey();

        return md5("{$packed_string}{$safe_key}");
    }
}