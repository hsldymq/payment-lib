<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\Config\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 查询转账订单接口.
 *
 * @link https://docs.open.alipay.com/api_28/alipay.fund.trans.order.query
 */
class FundTransOrderQuery implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use OpenAPIParameterMakerTrait;
    use DefaultSenderTrait;

    private const SIGN_FIELD = 'sign';
    private const CONTENT_FIELD = 'alipay_fund_trans_order_query_response';

    private AlipayConfigInterface $config;

    private array $bizContent = [
        'out_biz_no' => null,           // 与order_id二选一
        'order_id' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, [], ['out_biz_no', 'order_id']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);

        $parameters = $this->makeSignedParameters('alipay.fund.trans.order.query', $bizContent);

        return $parameters;
    }

    public function setOutBizNo(?string $outBizNo): self
    {
        $this->bizContent['out_biz_no'] = $outBizNo;

        return $this;
    }

    public function setOrderID(?string $id): self
    {
        $this->bizContent['order_id'] = $id;

        return $this;
    }
}