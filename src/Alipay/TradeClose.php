<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIRequestSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 统一收单交易关闭接口.
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.close 接口文档
 */
class TradeClose implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.trade.close';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const WITH_CERT = false;
    private const RESPONSE_CONTENT_FIELD = 'alipay_trade_close_response';

    private OpenAPIConfigInterface $config;

    private array $params = [
        'notify_url' => null,
        'timestamp' => null,
    ];

    private array $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
        'operator_id' => null,
    ];

    public function __construct(OpenAPIConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setNotifyURL(?string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setTradeNo(?string $trade_no): self
    {
        $this->bizContent['trade_no'] = $trade_no;

        return $this;
    }

    public function setOutTradeNo(?string $out_trade_no): self
    {
        $this->bizContent['out_trade_no'] = $out_trade_no;

        return $this;
    }

    public function setOperatorID(?string $id): self
    {
        $this->bizContent['operator_id'] = $id;

        return $this;
    }
}