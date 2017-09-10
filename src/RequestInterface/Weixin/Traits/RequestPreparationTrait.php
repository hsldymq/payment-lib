<?php
namespace Utils\PaymentVendor\RequestInterface\Weixin\Traits;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;

/**
 * @property string $uri
 * @property WeixinConfig $config
 */
trait RequestPreparationTrait
{
    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST',new Uri($this->uri));
        $body = stream_for(\array_to_xml($parameters));

        return $request->withBody($body);
    }
}