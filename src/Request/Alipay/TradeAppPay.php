<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use function GuzzleHttp\json_encode;

/**
 * APP支付.生成请求参数
 *
 * @see https://docs.open.alipay.com/204/105465
 */
class TradeAppPay implements ParameterMakerInterface
{
    use OpenAPIParameterMakerTrait;

    private AlipayConfigInterface $config;

    private array $params = [
        'notify_url' => null,
    ];

    private array $bizContent = [
        'body' => null,
        'subject' => null,
        'out_trade_no' => null,
        'timeout_express' => null,
        'total_amount' => null,
        'product_code' => 'QUICK_MSECURITY_PAY', // 必填参数(固定值)
        'goods_type' => null,
        'passback_params' => null,
        'promo_params' => null,
        'extend_params' => null,
        'enable_pay_channels' => null,
        'disable_pay_channels' => null,
        'store_id' => null,
        'ext_user_info' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_trade_no', 'subject', 'total_amount']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeSignedParameters('alipay.trade.app.pay', $bizContent);

        return $parameters;
    }

    public function setNotifyURL(string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setBody(?string $body): self
    {
        $this->bizContent['body'] = $body;

        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->bizContent['subject'] = $subject;

        return $this;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->bizContent['out_trade_no'] = $out_trade_no;

        return $this;
    }

    /**
     * @param int $minutes 单位:分钟
     *
     * @return self
     */
    public function setTimeoutExpress(?int $minutes): self
    {
        $this->bizContent['timeout_express'] = is_null($minutes) ? null : "{$minutes}m";

        return $this;
    }

    /**
     * 设置支付金额(单位分).
     *
     * @param int $amount
     *
     * @return self
     */
    public function setTotalAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->bizContent['total_amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setGoodsType(?string $type): self
    {
        $this->bizContent['goods_type'] = $type;

        return $this;
    }

    public function setPassbackParams(?string $params): self
    {
        $this->bizContent['passback_params'] = $params;

        return $this;
    }

    public function setPromoParams(?string $params): self
    {
        $this->bizContent['promo_params'] = $params;

        return $this;
    }

    public function setExtendParams(
        ?string $sysServiceProviderID,
        ?bool $needBuyerRealNamed,
        ?string $transMemo,
        ?int $HBFQNum,
        ?float $HBFQSellerPercent
    ): self {
        $data = ParameterHelper::packValidParameters([
            'sys_service_provider_id' => $sysServiceProviderID,
            'needBuyerRealnamed' => is_null($needBuyerRealNamed) ? null : ($needBuyerRealNamed ? 'T' : 'F'),
            'TRANS_MEMO' => $transMemo,
            'hb_fq_num' => "$HBFQNum",
            'hb_fq_seller_percent' => "$HBFQSellerPercent",
        ]);
        $this->bizContent['extend_params'] = $data ? json_encode($data) : null;

        return $this;
    }

    public function setEnablePayChannels(?array $channels): self
    {
        $this->bizContent['enable_pay_channels'] = implode(',', $channels ?? []) ?: null;

        return $this;
    }

    public function setDisablePayChannels(?array $channels): self
    {
        $this->bizContent['disable_pay_channels'] = implode(',', $channels ?? []) ?: null;

        return $this;
    }

    public function setStoreID(?string $id): self
    {
        $this->bizContent['store_id'] = $id;

        return $this;
    }

    public function setExtUserInfo(
        ?string $name = null,
        ?string $mobile = null,
        ?string $certType = null,
        ?string $certNo = null,
        ?int $minAge = null,
        ?bool $fixBuyer = null,
        ?bool $needCheckInfo = null
    ): self {
        $data = ParameterHelper::packValidParameters([
            'name' => $name,
            'mobile' => $mobile,
            'cert_type' => $certType,
            'cert_no' => $certNo,
            'min_age' => "$minAge",
            'fix_buyer' => is_null($fixBuyer) ? null : ($fixBuyer ? 'T' : 'F'),
            'need_check_info' => is_null($needCheckInfo) ? null : ($needCheckInfo ? 'T' : 'F'),
        ]);
        $this->bizContent['ext_user_info'] = $data ? json_encode($data) : null;

        return $this;
    }
}