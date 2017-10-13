<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\SignatureHelper\Wechat\Generator;

/**
 * 微信内H5调起支付.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6
 */
class WapPayInWeChat
{
    use NonceStrTrait;

    private const FIXED_SIGN_TYPE = 'MD5';

    private $config;

    /** @var \DateTime */
    private $datetime;

    private $params = [
        'package' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['package']);

        $parameters['appId'] = $this->config->getAppID();
        $parameters['timeStamp'] = $this->getTimestamp();
        $parameters['nonceStr'] = $this->getNonceStr();
        $parameters['signType'] = self::FIXED_SIGN_TYPE;
        $parameters['package'] = $this->params['package'];
        $parameters['paySign'] = (new Generator($this->config))->makeSign($parameters, self::FIXED_SIGN_TYPE);

        return $parameters;
    }

    public function setPrepayID(string $prepay_id): self
    {
        $this->params['package'] = "prepay_id={$prepay_id}";

        return $this;
    }

    public function setDatetime(\DateTime $dt): self
    {
        $this->datetime = $dt;

        return $this;
    }

    private function getTimestamp(): int
    {
        $datetime =  $this->datetime ?? new \DateTime('now', new \DateTimeZone('+0800'));

        return $datetime->getTimestamp();
    }
}