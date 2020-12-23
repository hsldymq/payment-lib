<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPI\CertConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPI\PKConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIRequestSenderTrait;
use Archman\PaymentLib\ClientFactoryInterface;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 统一收单线下交易查询.
 *
 * @method string sendRequest(?ClientFactoryInterface $clientFactory = null)
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.query
 */
class TradeQuery implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.trade.query';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const RESPONSE_CONTENT_FIELD = 'alipay_trade_query_response';

    private CertConfigInterface|PKConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
        'org_pid' => null,
        'query_options' => null,
    ];

    public function __construct(CertConfigInterface|PKConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setOutTradeNo(?string $out_trade_no): self
    {
        $this->bizContent['out_trade_no'] = $out_trade_no;

        return $this;
    }

    public function setTradeNo(?string $trade_no): self
    {
        $this->bizContent['trade_no'] = $trade_no;

        return $this;
    }

    public function setOrgPid(?string $pid): self
    {
        $this->bizContent['org_pid'] = $pid;

        return $this;
    }

    public function setQueryOptions(?array $options): self
    {
        $this->bizContent['query_options'] = $options ? json_encode(array_values($options)) : null;

        return $this;
    }
}