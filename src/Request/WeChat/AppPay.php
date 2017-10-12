<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\MutableDateTimeInterface;
use Archman\PaymentLib\Request\Traits\MutableDateTimeTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_12&index=2
 */
class AppPay implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

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
        $parameters['noncestr'] = md5(microtime(true));
        $parameters['timestamp'] = $this->getTimestamp();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setPrepayID(string $prepayID): self
    {
        $this->params['prepayid'] = $prepayID;

        return $this;
    }

    public function setDatetime(\DateTime $d): self
    {
        $this->datetime = $d;

        return $this;
    }

    private function getTimestamp(): int
    {
        $this->datetime->getTimestamp();
    }
}