<?php

namespace Archman\PaymentLib\ConfigManager;

interface WeChatConfigInterface
{
    public function getAppID(): string;

    /**
     * 商户ID.
     * @return string
     */
    public function getMerchantID(): string;

    /**
     * 根证书文件路径.
     * @return string
     */
    public function getRootCAPath(): ?string;

    /**
     * SSL密钥文件路径.
     * @return string
     */
    public function getSSLKeyPath(): ?string;

    /**
     * SSL密钥密码. 为设置返回null
     * @return null|string
     */
    public function getSSLKeyPassword(): ?string;

    /**
     * SSL证书文件路径.
     * @return string
     */
    public function getClientCertPath(): ?string;

    /**
     * SSL证书密码. 未设置返回null.
     * @return null|string
     */
    public function getClientCertPassword(): ?string;

    public function getAPIKey(): string;

    public function getDefaultSignType(): string;
}