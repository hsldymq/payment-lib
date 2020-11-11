<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\Config\WeChatConfigInterface;

class WeChatConfig implements WeChatConfigInterface
{
    private array $config;

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

    public function getSignType(): string
    {
        return 'MD5';
    }

    public function getClientCertPassword(): ?string
    {
        return $this->config['sslCertPassword'] ?? null;
    }

    public function getClientCertPath(): ?string
    {
        return $this->config['sslCert'] ?? null;
    }

    public function getRootCAPath(): ?string
    {
        return $this->config['rootCA'] ?? null;
    }

    public function getSSLKeyPassword(): ?string
    {
        return $this->config['sslKeyPassword'] ?? null;
    }

    public function getSSLKeyPath(): ?string
    {
        return $this->config['sslKey'] ?? null;
    }

    public function isSandbox(): bool
    {
        return $this->config['isSandbox'];
    }
}