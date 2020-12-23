<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config\OpenAPI;

/**
 * 普通公钥配置.
 */
interface PKConfigInterface extends BaseConfigInterface
{
    /**
     * 返回支付宝公钥.
     *
     * @return string 返回公钥文件路径或者公钥内容, 如果是公钥内容必须为PEM格式.
     */
    public function getAlipayPublicKey(): string;
}