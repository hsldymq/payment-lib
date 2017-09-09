<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay\Traits;

use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;
use Utils\PaymentVendor\SignatureHelper\Alipay\Generator;

/**
 * @property AlipayConfig $config
 * @property string $sign_type
 */
trait ParametersMakerTrait
{
    private function makeSignedParameters(
        string $method,
        array $biz_content,
        array $extra_data = [],
        string $format = 'JSON',
        string $charset = 'utf-8',
        string $version = '1.0'
    ): array {
        $now = $this->now();
        $parameters = [
            'app_id' => $this->config->getAppID(),
            'method' => $method,
            'format' => $format,
            'charset' => $charset,
            'sign_type' => $this->sign_type,
            'timestamp' => $now->format('Y-m-d H:i:s'),
            'version' => $version,
        ];
        $parameters = array_merge($extra_data, $parameters);
        $parameters['biz_content'] = \GuzzleHttp\json_encode($biz_content, JSON_FORCE_OBJECT);
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    private function now(): \DateTime
    {
        if ($this instanceof MutableDateTimeInterface) {
            return $this->getDateTime();
        }

        return new \DateTime('now', new \DateTimeZone('+0800'));
    }
}