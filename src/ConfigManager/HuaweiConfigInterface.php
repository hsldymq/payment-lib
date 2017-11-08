<?php
namespace Archman\PaymentLib\ConfigManager;

interface HuaweiConfigInterface
{
    public function getAppID(): string;

    /**
     * 支付ID.
     * @return string
     */
    public function getMerchantID(): string;

    /**
     * 支付公钥.
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * 支付私钥.
     * @return string
     */
    public function getPrivateKey(): string;
}