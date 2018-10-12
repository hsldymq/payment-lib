<?php

namespace Archman\PaymentLib\ConfigManager;

interface GoogleConfigInterface
{
    public function getPackageName(): string;

    /**
     * 应用的许可密钥(License Key).
     * 通过在Google Play Console中Services & APIs中获取.
     * 返回的密钥值应该为PKCS格式.
     * @return string
     */
    public function getLicenseKey(): string;
}