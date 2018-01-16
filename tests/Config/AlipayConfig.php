<?php
namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

class AlipayConfig implements AlipayConfigInterface
{
    private $appID;

    private $partnerID;

    private $openAPIDefaultSignType;

    private $openAPIPrivateKeys;

    private $openAPIAlipayPublicKeys;

    private $MAPIDefaultSignType;

    private $MAPIPrivateKeys;

    private $MAPIAlipayPublicKeys;

    public function __construct(array $config)
    {
        $this->appID = $config['appID'];
        $this->partnerID = $config['partnerID'];

        $this->openAPIDefaultSignType = $config['openAPIDefaultSignType'];
        $this->openAPIPrivateKeys = $config['openAPIPrivateKeys'];
        $this->openAPIAlipayPublicKeys = $config['openAPIAlipayPublicKeys'];

        $this->MAPIDefaultSignType = $config['MAPIDefaultSignType'];
        $this->MAPIPrivateKeys = $config['MAPIPrivateKeys'];
        $this->MAPIAlipayPublicKeys = $config['MAPIAlipayPublicKeys'];
    }

    public function getAppID(): string
    {
        return $this->appID;
    }

    public function getPartnerID(): string
    {
        return $this->partnerID;
    }

    public function getOpenAPIDefaultSignType(): string
    {
        return $this->openAPIDefaultSignType;
    }

    public function getOpenAPIPrivateKey(?string $signType = null): string
    {
        $signType = $signType ?? $this->getOpenAPIDefaultSignType();

        return $this->openAPIPrivateKeys[$signType];
    }

    public function getOpenAPIAlipayPublicKey(string $signType): string
    {
        return $this->openAPIAlipayPublicKeys[$signType];
    }

    public function getMAPIDefaultSignType(): string
    {
        return $this->MAPIDefaultSignType;
    }

    public function getMAPIPrivateKey(?string $signType = null): string
    {
        $signType = $signType ?? $this->getMAPIDefaultSignType();

        return $this->MAPIPrivateKeys[$signType];
    }

    public function getMAPIAlipayPublicKey(string $signType): string
    {
        return $this->MAPIAlipayPublicKeys[$signType];
    }

    public function setOpenAPIDefaultSignType(string $signType): self
    {
        $this->openAPIDefaultSignType = $signType;

        return $this;
    }

    public function setMAPIDefaultSignType(string $signType): self
    {
        $this->MAPIDefaultSignType = $signType;

        return $this;
    }
}