<?php
namespace Archman\PaymentLib\ConfigManager;

interface AlipayConfigInterface
{
    public function getAppID(): string;

    /**
     * 合作伙伴ID.
     * @return string
     */
    public function getPartnerID(): string;

    /**
     * 支付宝公钥.
     * @param null|string $algo
     * @return string
     */
    public function getAlipayPublicKey(?string $algo = null): string;

    /**
     * 开放平台应用密钥.
     * @param null|string $algo
     * @return string
     */
    public function getOpenAPIPrivateKey(?string $algo = null): string;

    /**
     * mapi网关密钥.
     * @param null|string $algo
     * @return string
     */
    public function getMAPIPrivateKey(?string $algo = null): string;

    /**
     * 应用公钥证书文件路径.
     * @param null|string $algo
     * @return string
     */
    public function getAppCertPath(?string $algo = null): string;
}