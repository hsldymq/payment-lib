<?php
namespace Archman\PaymentLib\ConfigManager;

interface WechatConfigInterface
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
    public function getClientKeyPath(): ?string;

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

    /**
     * SSL密钥密码. 为设置返回null
     * @return null|string
     */
    public function getClientKeyPassword(): ?string;

    public function getApiKey(): string;

    public function getDefaultSignType(): string;
}