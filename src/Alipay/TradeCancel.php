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
 * 统一收单交易撤销接口.
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.cancel/ 接口文档
 */
class TradeCancel implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.trade.cancel';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const RESPONSE_CONTENT_FIELD = 'alipay_trade_cancel_response';

    private CertConfigInterface|PKConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
    ];

    public function __construct(CertConfigInterface|PKConfigInterface $config)
    {
        $this->config = $config;
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
}