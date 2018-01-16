<?php
namespace Archman\PaymentLib\Request\Alipay\Traits;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
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
    /** @var \DateTime */
    private $datetime;

    private function makeSignedParameters(
        string $method,
        array $bizContent,
        string $format = 'JSON',
        string $charset = 'utf-8',
        string $version = '1.0'
    ): array {
        $signType = $this->signType ?? $this->config->getOpenAPIDefaultSignType();

        $parameters = ParameterHelper::packValidParameters($this->params ?? []);
        $parameters['app_id'] = $this->config->getAppID();
        $parameters['method'] = $method;
        $parameters['format'] = $format;
        $parameters['charset'] = $charset;
        $parameters['sign_type'] = $signType;
        $parameters['timestamp'] = $this->getDatetime();
        $parameters['version'] = $version;
        $parameters['biz_content'] = json_encode($bizContent, JSON_FORCE_OBJECT);
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType);

        return $parameters;
    }

    public function setTimestamp(\DateTime $dt)
    {
        $this->datetime = $dt;

        return $this;
    }

    private function getDatetime(): string
    {
        $dt = $this->datetime ?? (new \DateTime('now', new \DateTimeZone('+0800')));

        return $dt->format('Y-m-d H:i:s');
    }
}