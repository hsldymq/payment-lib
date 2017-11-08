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

    /**
     * 默认使用的签名类型(RSA, RSA2).
     * @return string
     */
    public function getDefaultSignType(): string;
}