<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Helper;

use Archman\PaymentLib\Exception\ContextualException;

class CertHelper
{
    /**
     * 在支付宝官方提供的SDK中, 提取公钥证书中的DN时, PHP版本只提取LN为 sha1WithRSAEncryption 和 sha256WithRSAEncryption 证书DN参与序列号计算
     * 但是在C#和Java版本中是按照OID来判断, 只要父OID为1.2.840.113549.1.1的都会将DN参与序列号计算.
     *
     * PHP无法获取到OID, 所以这里直接将当前所有属于1.2.840.113549.1.1的LN列出,尽可能与官方SDK的Java和C#版的兼容.
     *
     * @see http://oid-info.com/get/1.2.840.113549.1.1 列表参考此处
     *
     * @var bool[]
     */
    private static array $listAlgo = [
        'rsaEncryption' => true,                // OID: 1.2.840.113549.1.1.1
        'md2WithRSAEncryption' => true,         // OID: 1.2.840.113549.1.1.2
        'md4withRSAEncryption' => true,         // OID: 1.2.840.113549.1.1.3
        'md5WithRSAEncryption' => true,         // OID: 1.2.840.113549.1.1.4
        'sha1WithRSAEncryption' => true,        // OID: 1.2.840.113549.1.1.5
        'sha-1WithRSAEncryption' => true,       // OID: 1.2.840.113549.1.1.5
        'sha1-with-rsa-signature' => true,      // OID: 1.2.840.113549.1.1.5
        'rsaOAEPEncryptionSET' => true,         // OID: 1.2.840.113549.1.1.6
        'ripemd160WithRSAEncryption' => true,   // OID: 1.2.840.113549.1.1.6
        'id-RSAES-OAEP' => true,                // OID: 1.2.840.113549.1.1.7
        'id-mgf1' => true,                      // OID: 1.2.840.113549.1.1.8
        'id-pSpecified' => true,                // OID: 1.2.840.113549.1.1.9
        'rsassa-pss' => true,                   // OID: 1.2.840.113549.1.1.10
        'id-RSASSA-PSS' => true,                // OID: 1.2.840.113549.1.1.10
        'sha256WithRSAEncryption' => true,      // OID: 1.2.840.113549.1.1.11
        'sha384WithRSAEncryption' => true,      // OID: 1.2.840.113549.1.1.12
        'sha512WithRSAEncryption' => true,      // OID: 1.2.840.113549.1.1.13
        'sha224WithRSAEncryption' => true,      // OID: 1.2.840.113549.1.1.14
        'sha512-224WithRSAEncryption' => true,  // OID: 1.2.840.113549.1.1.15
        'sha512-256WithRSAEncryption' => true,  // OID: 1.2.840.113549.1.1.16
    ];

    /**
     * 生成证书序列号.
     *
     * @param string $cert 证书文件路径或证书内容. 内容应该符合PEM格式.
     *
     * @return string
     * @throws ContextualException
     */
    public static function getCertSN(string $cert): string
    {
        if (is_file($cert)) {
            $cert = self::getFileContentOrThrow($cert);
        }
        $parsed = openssl_x509_parse($cert);
        if ($parsed === false) {
            throw new ContextualException([], openssl_error_string());
        }

        return md5(self::getIssuerDN($parsed['issuer']).$parsed['serialNumber']);
    }

    /**
     * 生成根证书序列号.
     *
     * @param string $cert 证书文件路径证书内容. 内容应该符合PEM格式.
     *
     * @return string
     * @throws ContextualException
     */
    public static function getRootCertSN(string $cert): string
    {
        if (is_file($cert)) {
            $cert = self::getFileContentOrThrow($cert);
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

            $ln = $parsed['signatureTypeLN'];
            if (!(self::$listAlgo[$ln] ?? false)) {
                continue;
            }

            $serialNumber = $parsed['serialNumber'];
            if (str_starts_with($serialNumber, '0x')) {
                $serialNumber = self::hex2dec($serialNumber);
            }


            $v = md5(self::getIssuerDN($parsed['issuer']).$serialNumber);
            $sn .= ($sn ? "_{$v}" : $v);
        }

        return $sn;
    }

    /**
     * 从公钥证书中提取公钥.
     *
     * @param string $cert 证书文件路径或证书内容. 内容应该符合PEM格式.
     *
     * @return string
     * @throws ContextualException
     */
    public static function extractPublicKey(string $cert): string
    {
        if (is_file($cert)) {
            $cert = self::getFileContentOrThrow($cert);
        }
        $pkey = openssl_pkey_get_public($cert);
        if ($pkey) {
            $keyData = openssl_pkey_get_details($pkey);
        }
        if (!$pkey || !$keyData) {
            throw new ContextualException([], openssl_error_string());
        }

        return $keyData['key'];
    }

    /**
     * 生成证书Issuer Distinguished Name.
     *
     * @param array $issuer
     *
     * @return string
     */
    public static function getIssuerDN(array $issuer): string
    {
        if (key($issuer) !== 'CN') {
            $issuer = array_reverse($issuer);
        }
        $dn = [];
        foreach ($issuer as $k => $v) {
            $dn[] = "{$k}={$v}";
        }

        return implode(',', $dn);
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

    private static function getFileContentOrThrow(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new ContextualException([], error_get_last()['message']);
        }
        return $content;
    }
}