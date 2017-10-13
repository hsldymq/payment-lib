<?php
namespace Archman\PaymentLib\RequestInterface\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\DataParser;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * @property string $uri
 * @property WeChatConfigInterface $config
 */
trait RequestPreparationTrait
{
    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST',new Uri($this->uri));
        $body = stream_for(DataParser::arrayToXML($parameters));

        return $request->withBody($body);
    }
}