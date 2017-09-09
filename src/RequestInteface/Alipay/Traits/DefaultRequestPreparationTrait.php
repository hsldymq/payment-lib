<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay\Traits;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\build_query;
use Utils\PaymentVendor\RequestInterface\Traits\CertVerificationLessTrait;

/**
 * @method array makeParameters
 */
trait DefaultRequestPreparationTrait
{
    use CertVerificationLessTrait;

    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST','https://openapi.alipay.com/gateway.do?'.build_query($parameters));

        return $request;
    }
}