<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIRequestSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 转账业务单据查询.
 *
 * @see https://docs.open.alipay.com/api_28/alipay.fund.trans.common.query 接口文档
 */
class FundTransCommonQuery implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.fund.trans.common.query';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const RESPONSE_CONTENT_FIELD = 'alipay_fund_trans_common_query_response';

    private OpenAPIConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'product_code' => null,
        'biz_scene' => null,
        'out_biz_no' => null,
        'order_id' => null,
        'pay_fund_order_id' => null,
    ];

    public function __construct(OpenAPIConfigInterface $config)
    {
        $this->config = $config;
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