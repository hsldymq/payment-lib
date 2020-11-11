<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config;

interface HuaweiConfigInterface
{
    public function getAppID(): string;

    /**
     * 支付ID.
     *
     * @return string
     */
    public function getMerchantID(): string;

    /**
     * 支付公钥.
     *
     * 返回的密钥值应该为PKCS格式.
     *
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * 支付私钥.
     *
     * 返回的密钥值应该为PKCS格式.
     *
     * @return string
     */
    public function getPrivateKey(): string;
}