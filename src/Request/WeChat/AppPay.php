<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 生成调起App支付接口参数.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_12&index=2
 */
class AppPay
{
    use NonceStrTrait;
    use EnvironmentTrait;

    private $config;

    /** @var \DateTime */
    private $datetime;

    private $params = [
        'prepayid' => null,
        'package' => 'Sign=WXPay',
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['prepayid', 'package']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['partnerid'] = $this->config->getMerchantID();
        $parameters['noncestr'] = $this->getNonceStr();
        $parameters['timestamp'] = $this->getTimestamp();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setPrepayID(string $id): self
    {
        $this->params['prepayid'] = $id;

        return $this;
    }

    /**
     * 用于生成timestamp.
     * @param \DateTime|null $dt
     * @return AppPay
     */
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