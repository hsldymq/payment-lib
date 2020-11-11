<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Config\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\SignatureHelper\Wechat\Generator;

/**
 * 微信内H5调起支付.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6
 */
class WapPayInWeChat implements ParameterMakerInterface
{
    use NonceStrTrait;

    private WeChatConfigInterface $config;

    private string $signType = 'MD5';

    /** @var \DateTime */
    private \DateTime $datetime;

    private array $params = [
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
        $parameters['signType'] = $this->signType;
        $parameters['package'] = $this->params['package'];
        $parameters['paySign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setPrepayID(string $id): self
    {
        $this->params['package'] = "prepay_id={$id}";

        return $this;
    }

    public function setDatetime(?\DateTime $dt): self
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