<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 转账业务单据查询.
 *
 * @see https://docs.open.alipay.com/api_28/alipay.fund.trans.common.query
 */
class FundTransCommonQuery implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use OpenAPIParameterMakerTrait;
    use DefaultSenderTrait;

    private const SIGN_FIELD = 'sign';
    private const CONTENT_FIELD = 'alipay_fund_trans_common_query_response';

    private AlipayConfigInterface $config;

    private array $bizContent = [
        'product_code' => null,
        'biz_scene' => null,
        'out_biz_no' => null,
        'order_id' => null,
        'pay_fund_order_id' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $bizContent = ParameterHelper::packValidParameters($this->bizContent);

        $parameters = $this->makeSignedParameters('alipay.fund.trans.common.query', $bizContent);

        return $parameters;
    }

    public function setProductCode(?string $code): self
    {
        $this->bizContent['product_code'] = $code;

        return $this;
    }

    public function setBizScene(?string $scene): self
    {
        $this->bizContent['biz_scene'] = $scene;

        return $this;
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

    public function setPayFundOrderID(?string $id): self
    {
        $this->bizContent['pay_fund_order_id'] = $id;

        return $this;
    }
}