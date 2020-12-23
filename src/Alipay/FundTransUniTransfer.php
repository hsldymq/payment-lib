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
 * 单笔转账接口.
 *
 * @see https://docs.open.alipay.com/api_28/alipay.fund.trans.uni.transfer 接口文档
 */
class FundTransUniTransfer implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIRequestSenderTrait;

    private const METHOD = 'alipay.fund.trans.uni.transfer';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const RESPONSE_CONTENT_FIELD = 'alipay_fund_trans_uni_transfer_response';

    private CertConfigInterface|PKConfigInterface $config;

    private array $params = [
        'timestamp' => null,
    ];

    private array $bizContent = [
        'out_biz_no' => null,
        'trans_amount' => null,
        'product_code' => null,
        'biz_scene' => null,
        'order_title' => null,
        'original_order_id' => null,
        'payee_info' => null,
        'remark' => null,
        'business_params' => null,
    ];

    public function __construct(CertConfigInterface|PKConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * 设置out_biz_no参数.
     *
     * @param string|null $outBizNo
     *
     * @return $this
     */
    public function setOutBizNo(?string $outBizNo): self
    {
        $this->bizContent['out_biz_no'] = $outBizNo;

        return $this;
    }

    /**
     * 设置trans_amount参数.
     *
     * @param int|null $amount 单位: 分
     *
     * @return self
     */
    public function setTransAmount(?int $amount): self
    {
        if ($amount !== null) {
            $amount = bcdiv(strval($amount), '100', 2);
        }
        $this->bizContent['trans_amount'] = $amount;

        return $this;
    }

    /**
     * 设置product_code参数.
     *
     * @param string|null $code
     *
     * @return $this
     */
    public function setProductCode(?string $code)
    {
        $this->bizContent['product_code'] = $code;

        return $this;
    }

    /**
     * 设置biz_scene参数.
     *
     * @param string|null $scene
     *
     * @return $this
     */
    public function setBizScene(?string $scene): self
    {
        $this->bizContent['biz_scene'] = $scene;

        return $this;
    }

    /**
     * 设置order_title参数.
     *
     * @param string|null $title
     *
     * @return $this
     */
    public function setOrderTitle(?string $title): self
    {
        $this->bizContent['order_title'] = $title;

        return $this;
    }

    /**
     * 设置original_order_id参数.
     *
     * @param string|null $id
     *
     * @return $this
     */
    public function setOriginalOrderID(?string $id): self
    {
        $this->bizContent['original_order_id'] = $id;

        return $this;
    }

    /**
     * 设置payee_info参数.
     *
     * @param array|null $info
     *
     * @return $this
     * @throws
     */
    public function setPayeeInfo(?array $info): self
    {
        $this->bizContent['payee_info'] = $info ? json_encode($info, JSON_THROW_ON_ERROR) : null;

        return $this;
    }

    /**
     * 设置remark参数.
     *
     * @param string|null $remark
     *
     * @return $this
     */
    public function setRemark(?string $remark): self
    {
        $this->bizContent['remark'] = $remark;

        return $this;
    }

    /**
     * 设置business_params参数.
     *
     * @param string|null $params
     *
     * @return $this
     */
    public function setBusinessParams(?string $params): self
    {
        $this->bizContent['business_params'] = $params;

        return $this;
    }
}