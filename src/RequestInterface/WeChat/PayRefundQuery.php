<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\ResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RootCATrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;

/**
 * 查询退款.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_5&index=7
 */
class PayRefundQuery implements RequestableInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use RootCATrait;

    private $config;

    private $sign_type = 'MD5';

    private $uri = 'https://api.mch.weixin.qq.com/pay/refundquery';

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
        $parameters['nonce_str'] = md5(microtime(true));
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

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