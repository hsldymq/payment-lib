<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config;

interface OpenAPIConfigInterface extends ConfigInterface
{
    /**
     * 返回签名类型.
     *
     * @return string RSA/RSA2
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

    /**
     * 获得应用公钥证书.
     *
     * @return string 返回证书文件路径或者证书内容, 如果是公钥内容必须为PEM格式.
     */
    public function getCert(): string;

    /**
     * 获得支付宝公钥根证书.
     *
     * @return string 返回证书文件路径或者证书内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAlipayRootCert(): string;

    /**
     * 获得支付宝公钥证书.
     *
     * @return string 返回证书文件路径或者证书内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAlipayCert(): string;

    /**
     * 是否开启公钥证书, 返回true则会在请求中增加证书序列号, 验证响应时也会使用证书公钥进行验签名.
     *
     * @return bool
     */
    public function isCertEnabled(): bool;

    /**
     * 返回AES密钥.
     *
     * @return string
     *
     * @see https://docs.open.alipay.com/common/104567 查看关于AES加密接口内容的作用
     */
    public function getAESKey(): string;

    /**
     * 是否开启AES加密.
     *
     * @return bool
     */
    public function isAESEncryptionEnabled(): bool;

    /**
     * 是否是沙箱环境配置.
     *
     * API会根据此决定请求支付宝生产环境地址还是沙箱地址.
     *
     * @return bool
     */
    public function isSandBox(): bool;
}