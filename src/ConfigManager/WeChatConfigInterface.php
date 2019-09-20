<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager;

interface WeChatConfigInterface
{
    public function getAppID(): string;

    /**
     * 商户ID.
     *
     * @return string
     */
    public function getMerchantID(): string;

    /**
     * 根证书文件路径.
     *
     * @return string|null 为设置返回null
     */
    public function getRootCAPath(): ?string;

    /**
     * SSL密钥文件路径.
     *
     * @return string|null 为设置返回null
     */
    public function getSSLKeyPath(): ?string;

    /**
     * SSL密钥密码.
     *
     * @return string|null 为设置返回null
     */
    public function getSSLKeyPassword(): ?string;

    /**
     * SSL证书文件路径.
     *
     * @return string|null
     */
    public function getClientCertPath(): ?string;

    /**
     * SSL证书密码.
     *
     * @return string|null 未设置返回null
     */
    public function getClientCertPassword(): ?string;

    /**
     * Api Key密钥.
     *
     * @return string
     */
    public function getAPIKey(): string;

    public function getDefaultSignType(): string;
}