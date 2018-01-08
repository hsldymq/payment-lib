<?php
namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;

class WeChatConfig implements WeChatConfigInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getAPIKey(): string
    {
        return $this->config['APIKey'];
    }

    public function getAppID(): string
    {
        return $this->config['appID'];
    }

    public function getMerchantID(): string
    {
        return $this->config['merchantID'];
    }

    public function getDefaultSignType(): string
    {
        return 'MD5';
    }

    public function getClientCertPassword(): ?string
    {
        return null;
    }

    public function getClientCertPath(): ?string
    {
        return null;
    }

    public function getRootCAPath(): ?string
    {
        return null;
    }

    public function getSSLKeyPassword(): ?string
    {
        return null;
    }

    public function getSSLKeyPath(): ?string
    {
        return null;
    }
}