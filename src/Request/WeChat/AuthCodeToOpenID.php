<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 授权码查询openid.
 * @link https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_13&index=9
 */
class AuthCodeToOpenID implements RequestableInterface
{
    private $config;

    private $uri = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';

    private $params = [
        'auth_code' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['auth_code']);

        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['auth_code'] = $this->params['auth_code'];
        $parameters['noncestr'] = $this->getNonceStr();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setAuthCode(string $code): self
    {
        $this->params['auth_code'] = $code;

        return $this;
    }

    private function getNonceStr(): string
    {
        return md5(microtime(true));
    }
}