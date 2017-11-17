<?php
namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;

class HuaweiConfig implements HuaweiConfigInterface
{
    private $appID;

    private $merchantID;

    private $privateKey;

    private $publicKey;

    public function __construct(array $config)
    {
        $this->appID = $config['appID'];
        $this->merchantID = $config['merchantID'];
        $this->privateKey = $config['privateKey'];
        $this->publicKey = $config['publicKey'];
    }

    public function getAppID(): string
    {
        return $this->appID;
    }

    public function getMerchantID(): string
    {
        return $this->merchantID;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}