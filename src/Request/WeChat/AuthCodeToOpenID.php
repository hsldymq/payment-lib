<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 授权码查询openid.
 * @link https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_13&index=9
 */
class AuthCodeToOpenID implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';

    private $config;

    private $params = [
        'auth_code' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(bool $withSign = true): array
    {
        ParameterHelper::checkRequired($this->params, ['auth_code']);

        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['auth_code'] = $this->params['auth_code'];
        $parameters['noncestr'] = $this->getNonceStr();
        $withSign && $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setAuthCode(string $code): self
    {
        $this->params['auth_code'] = $code;

        return $this;
    }
}