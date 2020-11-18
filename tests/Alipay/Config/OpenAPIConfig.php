<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Config;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;

class OpenAPIConfig implements OpenAPIConfigInterface
{
    private array $config;

    private string $signType;

    private bool $aesEncEnabled = false;

    public function __construct(array $config, string $signType)
    {
        $this->config = $config;
        $this->signType = $signType;
    }

    public function getAppID(): string
    {
        return $this->config['appID'];
    }

    public function getPID(): string
    {
        return $this->config['partnerID'];
    }

    public function getSignType(): string
    {
        return $this->signType;
    }

    public function getPrivateKey(): string
    {
        return $this->config['openAPIPrivateKeys'][$this->signType];
    }

    public function getAlipayPublicKey(): string
    {
        return $this->config['openAPIAlipayPublicKeys'][$this->signType];
    }

    public function getAESKey(): string
    {
        return $this->config['openAPIEncryptionKey'];
    }

    public function isAESEncryptionEnabled(): bool
    {
        return $this->aesEncEnabled;
    }

    public function enableAESEncrypt(bool $enabled)
    {
        $this->aesEncEnabled = $enabled;
    }
}