<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config\OpenAPI;

use Archman\PaymentLib\Alipay\Config\ConfigInterface;

/**
 * 公钥证书配置.
 */
interface BaseConfigInterface extends ConfigInterface
{
    /**
     * 返回签名类型.
     *
     * @return string 返回RSA/RSA2.
     */
    public function getSignType(): string;

    /**
     * 返回应用私钥.
     *
     * @return string 返回私钥文件路径或者私钥内容, 如果是私钥内容必须为PEM格式.
     */
    public function getPrivateKey(): string;

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
    public function isAESEnabled(): bool;

    /**
     * 是否是沙箱环境配置.
     *
     * API会根据此决定请求支付宝生产环境地址还是沙箱地址.
     *
     * @return bool
     */
    public function isSandBox(): bool;
}