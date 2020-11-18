<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Helper;

class AESEncryption
{
    /**
     * 当前仅支持AES/CBC/PKCS5Padding.
     *
     * @param string $data
     * @param string $key
     *
     * @return string
     */
    public static function encrypt(string $data, string $key): string
    {
        $iv = str_repeat("\0", 16);

        return openssl_encrypt($data, 'AES-128-CBC', base64_decode($key), 0, $iv);
    }

    /**
     * 当前仅支持AES/CBC/PKCS5Padding.
     *
     * @param string $encryptedData
     * @param string $key
     *
     * @return string
     */
    public static function decrypt(string $encryptedData, string $key): string
    {
        $iv = implode('', array_fill(0, 16, chr(0)));

        return openssl_decrypt($encryptedData, 'AES-128-CBC', base64_decode($key), 0, $iv);
    }
}
