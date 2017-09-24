<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Api\Exception\Logic\MakePaymentVendorParametersFailedException;
use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\DefaultRequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\DefaultResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;

/**
 * @link https://docs.open.alipay.com/api_1/alipay.trade.refund 文档地址
 */
class TradeRefund implements RequestableInterface, MutableDateTimeInterface
{
    use ParametersMakerTrait;
    use DefaultRequestPreparationTrait;
    use DefaultResponseHandlerTrait;
    use MutableDateTimeTrait;

    /** @var AlipayConfig */
    private $config;

    private $sign_type = 'RSA';

    private $response_data_field = 'alipay_trade_refund_response';

    private $response_sign_field = 'sign';

    private $biz_content = [
        'out_trade_no' => null,
        'trade_no' => null,
        'refund_amount' => null,
        'refund_reason' => null,
        'out_request_no' => null,
        'operator_id' => null,
        'store_id' => null,
        'terminal_id' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired(
            $this->biz_content,
            ['refund_amount'],
            ['trade_no','out_trade_no']
        );

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);
        $parameters = $this->makeSignedParameters('alipay.trade.refund', $biz_content);

        return $parameters;
    }

    /**
     * 与out_trade_no必设置其一或这两者都设置,不能都为空.
     * @param string $trade_no
     * @return TradeRefund
     */
    public function setTradeNo(string $trade_no): self
    {
        $this->biz_content['trade_no'] = $trade_no;

        return $this;
    }

    /**
     * 与trade_no必设置其一或这两者都设置,不能都为空.
     * @param string $out_trade_no
     * @return TradeRefund
     */
    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->biz_content['out_trade_no'] = $out_trade_no;

        return $this;
    }

    /**
     * 标识一次请求,对于支付服务来说,这里填退款事务ID. 必须设置,无论是一次退款,还是多次退款.
     * @param string $out_request_no
     * @return TradeRefund
     */
    public function setOutRequestNo(string $out_request_no): self
    {
        $this->biz_content['out_request_no'] = $out_request_no;

        return $this;
    }

    /**
     * @param int $amount 单位: 分
     * @return TradeRefund
     * @throws MakePaymentVendorParametersFailedException
     */
    public function setRefundAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);

        $this->biz_content['refund_amount'] = ParameterHelper::transUnitCentToYuan($amount);

        return $this;
    }

    public function setRefundReason(string $reason): self
    {
        $this->biz_content['refund_reason'] = $reason;

        return $this;
    }

    public function setOperatorID(string $operator_id): self
    {
        $this->biz_content['operator_id'] = $operator_id;

        return $this;
    }

    public function setStoreID(string $store_id): self
    {
        $this->biz_content['store_id'] = $store_id;

        return $this;
    }

    public function setTerminalID(string $terminal_id): self
    {
        $this->biz_content['terminal_id'] = $terminal_id;

        return $this;
    }
}