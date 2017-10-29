<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay\Traits;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\build_query;

/**
 * @property AlipayConfigInterface $config
 */
trait OpenAPIRequestPreparationTrait
{
    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST','https://openapi.alipay.com/gateway.do?'.build_query($parameters));

        return $request;
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }
}