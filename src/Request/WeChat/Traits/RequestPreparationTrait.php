<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\DataConverter;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @property WeChatConfigInterface $config
 */
trait RequestPreparationTrait
{
    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', $this->getUri());
        $body = stream_for(DataConverter::arrayToXML($parameters));

        return $request->withBody($body);
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }

    public function getUri(): UriInterface
    {
        $uri = new Uri(self::URI);
        if ($this->config->isSandbox()) {
            $path = '/sandboxnew/'.ltrim($uri->getPath(), '/');
            $uri = $uri->withPath($path);
        }

        return $uri;
    }
}