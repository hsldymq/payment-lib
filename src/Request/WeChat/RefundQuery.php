<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 查询退款.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_5&index=7
 */
class RefundQuery implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/refundquery';

    private $config;

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
        'out_refund_no' => null,
        'refund_id' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, [], ['transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setTransactionID(string $id): self
    {
        $this->params['transaction_id'] = $id;

        return $this;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function setOutRefundNo(string $no): self
    {
        $this->params['out_refund_no'] = $no;

        return $this;
    }

    public function setRefundID(string $id): self
    {
        $this->params['refund_id'] = $id;

        return $this;
    }
}