<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Helper;

use Archman\PaymentLib\Exception\ContextualException;

class CertHelper
{
    public static function getCertSN(string $cert): string
    {
        if (is_file($cert)) {
            $cert = file_get_contents($cert);
        }
        $parsed = openssl_x509_parse($cert);
        if ($parsed === false) {
            throw new ContextualException([], openssl_error_string());
        }

        return md5(self::stringifyIssuer(array_reverse($parsed['issuer'])).$parsed['serialNumber']);
    }

    public static function getRootCertSN(string $cert): string
    {
        if (is_file($cert)) {
            $cert = file_get_contents($cert);
        }
        $certArr = explode("-----END CERTIFICATE-----", $cert);
        $sn = '';
        foreach ($certArr as $eachCert) {
            if (trim($eachCert) === '') {
                continue;
            }
            $parsed = openssl_x509_parse("{$eachCert}-----END CERTIFICATE-----");
            if ($parsed === false) {
                throw new ContextualException([], openssl_error_string());
            }
            $serialNumber = $parsed['serialNumber'];
            if (strpos($serialNumber, '0x') === 0) {
                $serialNumber = self::hex2dec($serialNumber);
            }
            if (!in_array($parsed['signatureTypeLN'], ["sha1WithRSAEncryption", "sha256WithRSAEncryption"])) {
                continue;
            }

            $v = md5(self::stringifyIssuer(array_reverse($parsed['issuer'])).$serialNumber);
            $sn .= ($sn ? "_{$v}" : $v);
        }

        return $sn;
    }

    private static function stringifyIssuer(array $issuer)
    {
        $arrStr = [];
        foreach ($issuer as $key => $value) {
            $arrStr[] = "{$key}={$value}";
        }
        return implode(',', $arrStr);
    }

    private static function hex2dec(string $hex): string
    {
        $result = '0';
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $result = bcadd($result, bcmul(strval(@hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }

        return $result;
    }
}