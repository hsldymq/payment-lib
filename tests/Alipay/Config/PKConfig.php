<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Config;

use Archman\PaymentLib\Alipay\Config\OpenAPI\PKConfigInterface;

class PKConfig implements PKConfigInterface
{
    public function __construct(private array $config, private bool $aesEnabled)
    {
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
        return $this->config['signType'];
    }

    public function getPrivateKey(): string
    {
        return $this->config['openAPI']['appPrivateKey'] ?? '';
    }

    public function getAlipayPublicKey(): string
    {
        return $this->config['openAPI']['alipayPublicKey'] ?? '';
    }

    public function getAESKey(): string
    {
        return $this->config['openAPI']['aesKey'] ?? '';
    }

    public function isAESEnabled(): bool
    {
        return $this->aesEnabled;
    }

    public function isSandBox(): bool
    {
        return $this->config['isSandbox'] ?? false;
    }
}