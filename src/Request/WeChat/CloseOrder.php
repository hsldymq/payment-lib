<?php
namespace Archman\PaymentLib\Request\WeChat;
use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 关闭订单.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_3&index=5
 */
class CloseOrder implements RequestableInterface
{
    private $config;

    private $uri = 'https://api.mch.weixin.qq.com/pay/closeorder';

    private $params = [
        'out_trade_no' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['out_trade_no']);

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

    private function getNonceStr(): string
    {
        return md5(microtime(true));
    }
}