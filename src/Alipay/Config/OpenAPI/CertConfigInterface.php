<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config\OpenAPI;

/**
 * 公钥证书配置.
 */
interface CertConfigInterface extends BaseConfigInterface
{
    /**
     * 获得应用公钥证书.
     *
     * @return string 返回证书文件路径或者证书内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAppCert(): string;

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
}