<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPI\CertConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPI\PKConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIRequestSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 统一收单交易退款接口.
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.refund/ 文档地址
 */
class TradeRefund implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.trade.refund';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const RESPONSE_CONTENT_FIELD = 'alipay_trade_refund_response';

    private CertConfigInterface|PKConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'out_trade_no' => null,
        'trade_no' => null,
        'refund_amount' => null,
        'refund_currency' => null,
        'refund_reason' => null,
        'out_request_no' => null,
        'operator_id' => null,
        'store_id' => null,
        'terminal_id' => null,
        'goods_detail' => null,
        'refund_royalty_parameters' => null,
        'org_pid' => null,
        'query_options' => null,
    ];

    public function __construct(CertConfigInterface|PKConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string|null $out_trade_no
     *
     * @return self
     */
    public function setOutTradeNo(?string $out_trade_no): self
    {
        $this->bizContent['out_trade_no'] = $out_trade_no;

        return $this;
    }

    /**
     * @param string|null $trade_no
     *
     * @return self
     */
    public function setTradeNo(?string $trade_no): self
    {
        $this->bizContent['trade_no'] = $trade_no;

        return $this;
    }

    /**
     * @param int|null $amount 单位: 分
     *
     * @return self
     */
    public function setRefundAmount(?int $amount): self
    {
        if ($amount !== null) {
            $amount = bcdiv(strval($amount), '100', 2);
        }
        $this->bizContent['refund_amount'] = $amount;

        return $this;
    }

    public function setRefundCurrent(?string $currency): self
    {
        $this->bizContent['refund_currency'] = $currency;

        return $this;
    }

    public function setRefundReason(?string $reason): self
    {
        $this->bizContent['refund_reason'] = $reason;

        return $this;
    }

    /**
     * @param string|null $out_request_no
     *
     * @return TradeRefund
     */
    public function setOutRequestNo(?string $out_request_no): self
    {
        $this->bizContent['out_request_no'] = $out_request_no;

        return $this;
    }

    public function setOperatorID(?string $operator_id): self
    {
        $this->bizContent['operator_id'] = $operator_id;

        return $this;
    }

    public function setStoreID(?string $store_id): self
    {
        $this->bizContent['store_id'] = $store_id;

        return $this;
    }

    public function setTerminalID(?string $terminal_id): self
    {
        $this->bizContent['terminal_id'] = $terminal_id;

        return $this;
    }

    public function setGoodsDetail(?array $detail): self
    {
        $this->bizContent['goods_detail'] = $detail ? json_encode($detail, JSON_THROW_ON_ERROR) : null;

        return $this;
    }
}