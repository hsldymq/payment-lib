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
use Psr\Http\Message\UriInterface;

/**
 * @property WeChatConfigInterface $config
 */
trait RequestPreparationTrait
{
    /** @var bool 不调用setEnvironment时,默认向生产环境发起请求 */
    private $isProduction = true;

    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', $this->getUri());
        $body = stream_for(DataParser::arrayToXML($parameters));

        return $request->withBody($body);
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return (new RequestOption())->setRootCAFilePath($this->config->getRootCAPath());
    }

    public function setEnvironment(bool $isProduction): self
    {
        $this->isProduction = $isProduction;

        return $this;
    }

    private function getUri(): UriInterface
    {
        $uri = new Uri(self::URI);
        if (!$this->isProduction) {
            $path = '/sandboxnew/'.ltrim($uri->getPath(), '/');
            $uri = $uri->withPath($path);
        }

        return $uri;
    }
}