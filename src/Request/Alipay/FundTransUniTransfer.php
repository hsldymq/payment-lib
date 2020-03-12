<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Response\BaseResponse;

/**
 * 单笔转账.
 *
 * @see https://docs.open.alipay.com/api_28/alipay.fund.trans.uni.transfer
 */
class FundTransUniTransfer implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use OpenAPIParameterMakerTrait;

    private const SIGN_FIELD = 'sign';
    private const CONTENT_FIELD = 'alipay_fund_trans_toaccount_transfer_response';

    private AlipayConfigInterface $config;

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

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $bizContent = ParameterHelper::packValidParameters($this->bizContent);

        $parameters = $this->makeSignedParameters('alipay.fund.trans.uni.transfer', $bizContent);

        return $parameters;
    }

    public function setOutBizNo(?string $outBizNo): self
    {
        $this->bizContent['out_biz_no'] = $outBizNo;

        return $this;
    }

    /**
     * @param int $amount 单位: 分
     *
     * @return self
     */
    public function setTransAmount(int $amount): self
    {
        $this->bizContent['trans_amount'] = bcdiv($amount, 100, 2);

        return $this;
    }

    public function setProductCode(?string $code)
    {
        $this->bizContent['product_code'] = $code;

        return $this;
    }

    public function setBizScene(?string $scene): self
    {
        $this->bizContent['biz_scene'] = $scene;

        return $this;
    }

    public function setOrderTitle(?string $title): self
    {
        $this->bizContent['order_title'] = $title;

        return $this;
    }

    public function setOriginalOrderID(?string $id): self
    {
        $this->bizContent['original_order_id'] = $id;

        return $this;
    }

    public function setPayeeInfo(
        string $identity,
        string $identityType,
        ?string $name = null,
        ?array $extra = null
    ): self {
        $info = [
            'identity' => $identity,
            'identity_type' => $identityType,
        ];
        if ($name !== null) {
            $info['name'] = $name;
        }
        if ($extra !== null) {
            $info = array_merge($extra, $info);
        }
        $this->bizContent['payee_info'] = json_encode($info);

        return $this;
    }

    public function setRemark(?string $remark): self
    {
        $this->bizContent['remark'] = $remark;

        return $this;
    }

    public function setBusinessParams(?array $params = []): self
    {
        if ($params !== null) {
            $params = json_encode(($params));
        }
        $this->bizContent['business_params'] = $params;

        return $this;
    }

    public function send(?BaseClient $client = null): BaseResponse
    {
        return $client ? $client->sendRequest($this) : Client::sendRequest($this);
    }
}