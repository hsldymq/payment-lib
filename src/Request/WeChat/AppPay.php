<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Config\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 生成调起App支付接口参数.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_12&index=2
 */
class AppPay implements ParameterMakerInterface
{
    use NonceStrTrait;

    private WeChatConfigInterface $config;

    private array $params = [
        'prepayid' => null,
        'package' => null,
        'timestamp' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['partnerid'] = $this->config->getMerchantID();
        $parameters['noncestr'] = $this->getNonceStr();
        $parameters['package'] ??= 'Sign=WXPay';
        $parameters['timestamp'] ??= time();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setPrepayID(string $id): self
    {
        $this->params['prepayid'] = $id;

        return $this;
    }

    /**
     * 设置timestamp字段.
     *
     * @param \DateTime|null $dt
     *
     * @return self
     */
    public function setTimestamp(?\DateTime $dt): self
    {
        $this->params['timestamp'] = $dt ? $dt->getTimestamp() : null;

        return $this;
    }

    /**
     * 设置package字段.
     *
     * @param string|null $pkg
     *
     * @return $this
     */
    public function setPackage(?string $pkg): self
    {
        $this->params['package'] = $pkg;

        return $this;
    }
}