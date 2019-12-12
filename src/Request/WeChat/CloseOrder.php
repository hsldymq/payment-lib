<?php

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\GeneralResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 关闭订单.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_3&index=5
 */
class CloseOrder implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/closeorder';

    private WeChatConfigInterface $config;

    private array $params = [
        'out_trade_no' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function send(?BaseClient $client = null): GeneralResponse
    {
        $response = $client ? $client->sendRequest($this) : Client::send($this);

        return new GeneralResponse($this->handleResponse($response));
    }
}