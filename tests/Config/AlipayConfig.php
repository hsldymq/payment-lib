<?php
namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

class AlipayConfig implements AlipayConfigInterface
{
    private $appID;

    private $partnerID;

    private $alipayPublicKey;

    private $OpenAPIDefaultSignType;

    private $openAPIPrivateKey;

    private $openAPIPublicKey;

    private $MAPIDefaultSignType;

    private $MAPIPrivateKey;

    public function __construct(array $config)
    {
        $this->appID = $config['appID'];
        $this->partnerID = $config['partnerID'];

        $this->alipayPublicKey = $config['alipayPublicKey'];

        $this->OpenAPIDefaultSignType = $config['openAPIDefaultSignType'];
        $this->openAPIPrivateKey = $config['openAPIPrivateKey'];
        $this->openAPIPublicKey = $config['openAPIPublicKey'];

        $this->MAPIDefaultSignType = $config['MAPIDefaultSignType'];
        $this->MAPIPrivateKey = $config['MAPIPrivateKey'];
    }

    public function getAppID(): string
    {
        return $this->appID;
    }

    public function getPartnerID(): string
    {
        return $this->partnerID;
    }

    public function getAlipayPublicKey(?string $signType = null): string
    {
        return $this->alipayPublicKey;
    }

    public function getOpenAPIPrivateKey(?string $signType = null): string
    {
        return $this->openAPIPrivateKey;
    }

    public function getOpenAPIDefaultSignType(): string
    {
        return $this->OpenAPIDefaultSignType;
    }

    public function getMAPIPrivateKey(?string $signType = null): string
    {
        return $this->MAPIPrivateKey;
    }

    public function getMAPIDefaultSignType(): string
    {
        return $this->MAPIDefaultSignType;
    }

    public function getAppCertPath(?string $signType = null): ?string
    {
        return null;
    }
}