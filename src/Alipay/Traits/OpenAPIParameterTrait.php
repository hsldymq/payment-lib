<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Traits;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Helper\AESEncryption;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Alipay\Signature\Generator;

/**
 * @property OpenAPIConfigInterface $config
 * @property array $params
 * @property array $bizContent
 */
trait OpenAPIParameterTrait
{
    private \DateTimeInterface $datetime;

    public function makeParameters(): array
    {
        $parameters = ParameterHelper::packValidParameters($this->params);
        $bizContent = ParameterHelper::packValidParameters($this->bizContent);

        $parameters['app_id'] = $this->config->getAppID();
        $parameters['method'] = self::METHOD;
        $parameters['format'] = 'JSON';
        $parameters['charset'] = self::CHARSET;
        $parameters['sign_type'] = $this->config->getSignType();
        $parameters['timestamp'] = $this->getDatetimeStr();
        $parameters['version'] = self::VERSION;

        $bizContent = json_encode($bizContent ?: new class{}, JSON_THROW_ON_ERROR);
        if ($this->config->isAESEncryptionEnabled()) {
            $parameters['encrypt_type'] = 'AES';
            $bizContent = AESEncryption::encrypt($bizContent, $this->config->getAESKey());
        }
        $parameters['biz_content'] = $bizContent;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setAppAuthToken(?string $token): self
    {
        $this->params['app_auth_token'] = $token;

        return $this;
    }

    public function setTimestamp(\DateTimeInterface $dt): self
    {
        $this->datetime = $dt;

        return $this;
    }

    private function getDatetimeStr(): string
    {
        $dt = $this->datetime ?? (new \DateTime('now', new \DateTimeZone('+0800')));

        return $dt->format('Y-m-d H:i:s');
    }
}