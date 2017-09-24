<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\BidirectionalCertTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\ResponseHandlerTrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;

/**
 * TODO 有待验证
 * 申请退款接口.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_4&index=6
 */
class PayRefund implements RequestableInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use BidirectionalCertTrait;

    private $config;

    private $sign_type = 'MD5';

    private $uri = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
        'out_refund_no' => null,
        'total_fee' => null,
        'refund_fee' => null,
        'refund_fee_type' => null,
        'refund_desc' => null,
        'refund_account' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['out_refund_no', 'total_fee', 'refund_fee'], ['transaction_id', 'out_trade_no']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = md5(microtime(true));
        $parameters['sign_type'] = $this->sign_type;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

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

    public function setOutRefundNo(string $out_refund_no): self
    {
        $this->params['out_refund_no'] = $out_refund_no;

        return $this;
    }

    public function setTotalFee(int $fee): self
    {
        $this->params['total_fee'] = $fee;

        return $this;
    }

    public function setRefundFee(int $fee): self
    {
        $this->params['refund_fee'] = $fee;

        return $this;
    }

    public function setRefundFeeType(string $type): self
    {
        $this->params['refund_fee_type'] = $type;

        return $this;
    }

    public function setRefundDescription(string $desc): self
    {
        $this->params['refund_desc'] = $desc;

        return $this;
    }

    public function setRefundAccount(string $account): self
    {
        $this->params['refund_account'] = $account;

        return $this;
    }
}