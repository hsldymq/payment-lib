<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Traits;

use Archman\PaymentLib\Alipay\Config\OpenAPI\CertConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPI\PKConfigInterface;

/**
 * @property CertConfigInterface|PKConfigInterface $config
 */
trait OpenAPIEnvTrait
{
    public static string $baseURI = 'https://openapi.alipay.com/gateway.do';

    public static string $sandboxBaseURI = 'https://openapi.alipaydev.com/gateway.do';

    private function getBaseUri(): string
    {
        return $this->config->isSandBox() ? self::$sandboxBaseURI : self::$baseURI;
    }
}