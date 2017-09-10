<?php
namespace Utils\PaymentVendor\SignatureHelper\Alipay;

use Exception\InvalidPaymentPrivateKeyException;
use Exception\UnsupportedVendorSignTypeException;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;

class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(AlipayConfig $config)
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
                throw new UnsupportedVendorSignTypeException();
        }

        return $sign;
    }

    private function makeSignRSA(string $packed_string): string
    {
        $res = \openssl_get_privatekey($this->config->getAppPrivateKeyRSA());
        if (!$res) {
            throw new InvalidPaymentPrivateKeyException('Invalid Alipay Private Key');
        }

        \openssl_sign($packed_string, $sign, $res);
        \openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }

    private function makeSignRSA2(string $packed_string): string
    {
        $res = \openssl_get_privatekey($this->config->getAppPrivateKeyRSA2());
        if (!$res) {
            throw new InvalidPaymentPrivateKeyException('Invalid Alipay Private Key');
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