<?php

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 统一收单交易退款接口.
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.refund/ 文档地址
 */
class TradeRefund implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIParameterMakerTrait;
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;

    private $config;

    private const SIGN_FIELD = 'sign';

    private const CONTENT_FIELD = 'alipay_trade_refund_response';

    private $params = [
        'app_auth_token' => null,
    ];

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
        ParameterHelper::checkRequired($this->biz_content, ['refund_amount'], ['trade_no','out_trade_no']);

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);
        $parameters = $this->makeSignedParameters('alipay.trade.refund', $biz_content);

        return $parameters;
    }

    public function setAppAuthToken(?string $token): self
    {
        $this->params['app_auth_token'] = $token;

        return $this;
    }

    /**
     * @param string $out_trade_no
     *
     * @return self
     */
    public function setOutTradeNo(?string $out_trade_no): self
    {
        $this->biz_content['out_trade_no'] = $out_trade_no;

        return $this;
    }

    /**
     * @param string $trade_no
     *
     * @return self
     */
    public function setTradeNo(?string $trade_no): self
    {
        $this->biz_content['trade_no'] = $trade_no;

        return $this;
    }

    /**
     * @param int $amount 单位: 分
     *
     * @return self
     */
    public function setRefundAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->biz_content['refund_amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setRefundReason(?string $reason): self
    {
        $this->biz_content['refund_reason'] = $reason;

        return $this;
    }

    /**
     * @param string $out_request_no
     *
     * @return TradeRefund
     */
    public function setOutRequestNo(?string $out_request_no): self
    {
        $this->biz_content['out_request_no'] = $out_request_no;

        return $this;
    }

    public function setOperatorID(?string $operator_id): self
    {
        $this->biz_content['operator_id'] = $operator_id;

        return $this;
    }

    public function setStoreID(?string $store_id): self
    {
        $this->biz_content['store_id'] = $store_id;

        return $this;
    }

    public function setTerminalID(?string $terminal_id): self
    {
        $this->biz_content['terminal_id'] = $terminal_id;

        return $this;
    }
}