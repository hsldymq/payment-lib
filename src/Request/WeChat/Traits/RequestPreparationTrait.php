<?php
namespace Archman\PaymentLib\RequestInterface\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

trait RequestPreparationTrait
{
    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', new Uri(self::URI));
        $body = stream_for(DataParser::arrayToXML($parameters));

        return $request->withBody($body);
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        $option = new RequestOption();

        if (method_exists($this, 'customRequestOption')) {
            $option = $this->customRequestOption($option);
        }

        return $option;
    }
}