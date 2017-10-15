<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 查询订单.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2
 */
class OrderQuery implements RequestableInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/orderquery';

    private $config;

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, [], ['transaction_id', 'out_trade_no']);

        $signType = $this->config->getDefaultSignType();
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType);

        return $parameters;
    }

    public function setTransactionID(string $transaction_id): self
    {
        $this->params['transaction_id'] = $transaction_id;

        return $this;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->params['out_trade_no'] = $out_trade_no;

        return $this;
    }
}