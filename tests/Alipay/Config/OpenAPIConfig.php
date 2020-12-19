<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Config;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Helper\CertHelper;

class OpenAPIConfig implements OpenAPIConfigInterface
{
    private bool $aesEncEnabled = false;

    public function __construct(
        private array $config,
        private string $signType,
        private bool $certEnabled = false
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
        return $this->signType;
    }

    public function getPrivateKey(): string
    {
        return $this->config['openAPI'][$this->signType]['appPrivateKey'] ?? '';
    }

    public function getAlipayPublicKey(): string
    {
        if ($this->certEnabled) {
            CertHelper::extractPublicKey($this->config['openAPI'][$this->signType]['alipayCert'] ?? '');
        }

        return $this->config['openAPI'][$this->signType]['alipayPublicKey'] ?? '';
    }

    public function getCert(): string
    {
        return $this->config['openAPI'][$this->signType]['appCert'] ?? '';
    }

    public function getAlipayCert(): string
    {
        return $this->config['openAPI'][$this->signType]['alipayCert'] ?? '';
    }

    public function getAlipayRootCert(): string
    {
        return $this->config['openAPI'][$this->signType]['alipayRootCert'] ?? '';
    }

    public function isCertEnabled(): bool
    {
        return $this->certEnabled;
    }

    public function getAESKey(): string
    {
        return $this->config['openAPI']['aesKey'];
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