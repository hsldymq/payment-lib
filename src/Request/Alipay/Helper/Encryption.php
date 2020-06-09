<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay\Helper;

class Encryption
{
    /**
     * 当前仅支持AES/CBC/PKCS5Padding.
     *
     * @param string $data
     * @param string $encryptedKey
     *
     * @return string
     */
    public static function encrypt(string $data, string $encryptedKey): string
    {
        $iv = implode('', array_fill(0, 16, chr(0)));

        return openssl_encrypt($data, 'AES-128-CBC', base64_decode($encryptedKey), 0, $iv);
    }

    /**
     * 当前仅支持AES/CBC/PKCS5Padding.
     *
     * @param string $encryptedData
     * @param string $decryptedKey
     *
     * @return string
     */
    public static function decrypt(string $encryptedData, string $decryptedKey): string
    {
        $iv = implode('', array_fill(0, 16, chr(0)));

        return openssl_decrypt($encryptedData, 'AES-128-CBC', base64_decode($decryptedKey), 0, $iv);
    }
}
