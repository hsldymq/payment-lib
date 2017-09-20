<?php
namespace Archman\PaymentLib\ConfigManager;

interface WechatConfigInterface
{
    public function getAppID(): ?string;

    public function getMerchantID(): ?string;

    public function getRootCAPath(): ?string;

    public function getClientKeyPath(): ?string;

    public function getClientCertPath(): ?string;

    public function getApiKey(): ?string;
}