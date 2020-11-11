<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay\Traits;

use Archman\PaymentLib\Config\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Helper\Encryption;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\SignatureHelper\Alipay\Generator;
use function GuzzleHttp\json_encode;

/**
 * @property AlipayConfigInterface $config
 * @property string $signType
 * @property ?array $params
 */
trait OpenAPIParameterMakerTrait
{
    private \DateTime $datetime;

    private bool $encryptionEnabled = false;

    public function setTimestamp(\DateTime $dt): self
    {
        $this->datetime = $dt;

        return $this;
    }

    /**
     * 是否开启加密.
     *
     * @param bool $enable
     *
     * @return self
     */
    public function encrypt(bool $enable): self
    {
        $this->encryptionEnabled = $enable;

        return $this;
    }

    private function makeSignedParameters(
        string $method,
        array $bizContent,
        string $format = 'JSON',
        string $charset = 'utf-8',
        string $version = '1.0'
    ): array {
        $signType = $this->signType ?? $this->config->getOpenAPIDefaultSignType();
        $bizContent = json_encode($bizContent, JSON_FORCE_OBJECT);

        $parameters = ParameterHelper::packValidParameters($this->params ?? []);
        $parameters['app_id'] = $this->config->getAppID();
        $parameters['method'] = $method;
        $parameters['format'] = $format;
        $parameters['charset'] = $charset;
        $parameters['sign_type'] = $signType;
        $parameters['timestamp'] = $this->getDatetime();
        $parameters['version'] = $version;
        if ($this->encryptionEnabled) {
            $parameters['encrypt_type'] = 'AES';
            $bizContent = Encryption::encrypt($bizContent, $this->config->getOpenAPIEncryptionKey());
        }
        $parameters['biz_content'] = $bizContent;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType);

        return $parameters;
    }

    private function getDatetime(): string
    {
        $dt = $this->datetime ?? (new \DateTime('now', new \DateTimeZone('+0800')));

        return $dt->format('Y-m-d H:i:s');
    }
}