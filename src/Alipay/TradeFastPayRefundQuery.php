<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIRequestSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 统一收单交易退款查询.
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.fastpay.refund.query 接口文档
 */
class TradeFastPayRefundQuery implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.trade.fastpay.refund.query';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const WITH_CERT = false;
    private const RESPONSE_CONTENT_FIELD = 'alipay_trade_fastpay_refund_query_response';

    private OpenAPIConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
        'out_request_no' => null,
        'refund_reason' => null,
        'total_amount' => null,
        'refund_amount' => null,
        'refund_royaltys' => null,
        'gmt_refund_pay' => null,
        'refund_detail_item_list' => null,
        'send_back_fee' => null,
        'refund_settlement_id' => null,
        'present_refund_buyer_amount' => null,
        'present_refund_discount_amount' => null,
        'present_refund_mdiscount_amount' => null,
        'deposit_back_info' => null,
    ];

    public function __construct(OpenAPIConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setTradeNo(?string $no): self
    {
        $this->bizContent['trade_no'] = $no;

        return $this;
    }

    public function setOutTradeNo(?string $no): self
    {
        $this->bizContent['out_trade_no'] = $no;

        return $this;
    }

    public function setOutRequestNo(?string $no): self
    {
        $this->bizContent['out_request_no'] = $no;

        return $this;
    }
}