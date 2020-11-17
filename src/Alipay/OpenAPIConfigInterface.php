<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

interface OpenAPIConfigInterface extends ConfigInterface
{
    /**
     * 返回签名算法名称.
     *
     * @return string RSA/RSA2
     */
    public function getSignAlgo(): string;

    /**
     * 返回应用私钥.
     *
     * @return string 返回私钥文件或者私钥内容, 如果是私钥内容必须为PEM格式.
     */
    public function getPrivateKey(): string;

    /**
     * 返回支付宝公钥.
     *
     * @return string 返回公钥文件或者公钥内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAlipayPublicKey(): string;

    /**
     * 返回AES密钥.
     *
     * @return string|null 如果返回null则代表没有配置密钥,内容不会加密.
     *
     * @see https://docs.open.alipay.com/common/104567 查看关于AES加密接口内容的作用
     */
    public function getAESKey(): ?string;
}