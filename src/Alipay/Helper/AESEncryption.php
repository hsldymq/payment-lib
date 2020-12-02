<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Helper;

use Archman\PaymentLib\Exception\ContextualException;

class AESEncryption
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
        $iv = str_repeat("\0", 16);
        $result = openssl_encrypt($data, 'AES-128-CBC', base64_decode($encryptedKey), 0, $iv);
        if ($result === false) {
            throw new ContextualException(['encryptKey' => $encryptedKey, 'iv' => $iv], openssl_error_string());
        }

        return $result;
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
        $iv = str_repeat("\0", 16);
        $result = openssl_decrypt($encryptedData, 'AES-128-CBC', base64_decode($decryptedKey), 0, $iv);
        if ($result === false) {
            throw new ContextualException(['decryptKey' => $decryptedKey, 'iv' => $iv], openssl_error_string());
        }

        return $result;
    }
}
