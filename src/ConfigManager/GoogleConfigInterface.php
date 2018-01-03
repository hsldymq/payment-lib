<?php
namespace Archman\PaymentLib\ConfigManager;

interface GoogleConfigInterface
{
    public function getPackage(): string;

    /**
     * 获得服务账号密钥(只支持JSON格式), 返回json_decode后的关联数组形式.
     * @return array
     */
    public function getAuthPrivateKey(): array;

    /**
     * 应用的许可密钥(License Key).
     * 通过在Google Play Console中Services & APIs中获取.
     * 返回的密钥值应该为PEM格式,即收尾应该包含-----BEGIN PUBLIC KEY----- / -----END PUBLIC KEY-----对.
     * @return string
     */
    public function getLicenseKey(): string;
}