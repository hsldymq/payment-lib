<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config;

interface MAPIConfigInterface extends ConfigInterface
{
    /**
     * 返回签名类型.
     *
     * @return string
     */
    public function getSignType(): string;

    /**
     * 返回应用私钥.
     *
     * @return string 返回私钥文件路径或者私钥内容, 如果是私钥内容必须为PEM格式.
     */
    public function getPrivateKey(): string;

    /**
     * 返回支付宝公钥.
     *
     * @return string 返回公钥文件路径或者公钥内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAlipayPublicKey(): string;
}