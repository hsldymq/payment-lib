<?php

declare(strict_types=1);

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
 * 授权码查询openid.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_13&index=10
 */
class AuthCodeToOpenID implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';

    private WeChatConfigInterface $config;

    private array $params = [
        'auth_code' => null,
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
        $parameters['auth_code'] = $this->params['auth_code'];
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setAuthCode(string $code): self
    {
        $this->params['auth_code'] = $code;

        return $this;
    }

    public function send(?BaseClient $client = null): GeneralResponse
    {
        $response = $client ? $client->sendRequest($this) : Client::send($this);

        return new GeneralResponse($this->handleResponse($response));
    }
}