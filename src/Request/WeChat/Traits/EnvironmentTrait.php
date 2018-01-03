<?php
namespace Archman\PaymentLib\Request\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;

/**
 * @property WeChatConfigInterface $config
 */
trait EnvironmentTrait
{
    /** @var bool */
    private $isProduction;

    /** @var WeChatConfigInterface */
    private $sandboxConfig;

    /** @var WeChatConfigInterface */
    private $productionConfig;

    public function setEnvironment(bool $isProduction): self
    {
        $this->initEnvConfig();
        $this->isProduction = $isProduction;
        $this->config = $isProduction ? $this->productionConfig : $this->sandboxConfig;

        return $this;
    }

    public function setSandboxSignKey(string $key): self
    {
        $this->initEnvConfig();
        $this->sandboxConfig->setApiKey($key);

        return $this;
    }

    private function initEnvConfig()
    {
        if (!$this->productionConfig) {
            $this->productionConfig = $this->config;
            $this->sandboxConfig = (new class ($this->productionConfig) implements WeChatConfigInterface {
                /** @var WeChatConfigInterface */
                private $productionConfig;

                /** @var string */
                private $signKey;

                public function __construct(WeChatConfigInterface $prodConfig)
                {
                    $this->productionConfig = $prodConfig;
                }
                public function getAppID(): string { return $this->productionConfig->getAppID(); }
                public function getMerchantID(): string { return $this->productionConfig->getMerchantID(); }
                public function getRootCAPath(): ?string { return $this->productionConfig->getRootCAPath(); }
                public function getSSLKeyPath(): ?string { return $this->productionConfig->getSSLKeyPath(); }
                public function getSSLKeyPassword(): ?string { return $this->productionConfig->getSSLKeyPassword(); }
                public function getClientCertPath(): ?string { return $this->productionConfig->getClientCertPath(); }
                public function getClientCertPassword(): ?string { return $this->productionConfig->getClientCertPassword(); }
                public function getDefaultSignType(): string { return $this->productionConfig->getDefaultSignType(); }
                public function getAPIKey(): string { return $this->signKey; }
                public function setApiKey(string $key) { $this->signKey = $key; }
            });
        }
    }
}