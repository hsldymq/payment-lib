<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

class SandBoxSignKey implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey';

    private $config;

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', new Uri(self::URI));
        $body = stream_for(DataParser::arrayToXML($parameters));

        return $request->withBody($body);
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return (new RequestOption())->setRootCAFilePath($this->config->getRootCAPath());
    }
}