<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Config;

use Archman\PaymentLib\Alipay\Config\OpenAPI\CertConfigInterface;

class CertConfig implements CertConfigInterface
{
    public function __construct(
        private array $config,
        private bool $aesEnabled,
        private bool $certEnabled,
    ) {
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

    public function getAppCert(): string
    {
        return $this->config['openAPI']['appCert'] ?? '';
    }

    public function getAlipayCert(): string
    {
        return $this->config['openAPI']['alipayCert'] ?? '';
    }

    public function getAlipayRootCert(): string
    {
        return $this->config['openAPI']['alipayRootCert'] ?? '';
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