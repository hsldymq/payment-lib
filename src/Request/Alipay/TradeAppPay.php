<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\json_encode;

/**
 * APP支付.生成请求参数
 * @link https://docs.open.alipay.com/204/105465
 */
class TradeAppPay
{
    use ParametersMakerTrait;

    private $config;

    private $params = [
        'notify_url' => null
    ];

    private $bizContent = [
        'body' => null,
        'subject' => null,
        'out_trade_no' => null,
        'timeout_express' => null,
        'total_amount' => null,
        'seller_id' => null,
        'product_code' => 'QUICK_MSECURITY_PAY', // 必填参数(固定值)
        'goods_type' => null,
        'passback_params' => null,
        'promo_params' => [],
        'extend_params' => null,
        'enable_pay_channels' => null,
        'disable_pay_channels' => null,
        'store_id' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_trade_no', 'subject', 'total_amount']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeOpenAPISignedParameters('alipay.trade.app.pay', $bizContent);

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
     * @return self
     */
    public function setTimeoutExpress(?int $minutes): self
    {
        $minutes && $this->bizContent['timeout_express'] = "{$minutes}m";

        return $this;
    }

    /**
     * 设置支付金额(单位分).
     * @param int $amount
     * @return self
     */
    public function setTotalAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->bizContent['total_amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setSellerID(?string $id): self
    {
        $this->bizContent['seller_id'] = $id;

        return $this;
    }

    public function setGoodsType(?string $type): self
    {
        $this->bizContent['goods_type'] = $type;

        return $this;
    }

    public function setPassbackParams(?array $params): self
    {
        $this->bizContent['passback_params'] = build_query($params);

        return $this;
    }

    public function setPromoParams(?array $params): self
    {
        $this->bizContent['promo_params'] = json_encode($params);

        return $this;
    }

    public function setExtendParams(
        ?string $sysServiceProviderID,
        ?bool $needBuyerRealNamed,
        ?string $transMemo,
        ?int $HBFQNum,
        ?int $HBFQSellerPercent
    ): self {
        $rn = is_null($needBuyerRealNamed) ? null : $needBuyerRealNamed ? 'T' : 'F';
        $params = [
            'sys_service_provider_id' => $sysServiceProviderID,
            'needBuyerRealnamed' => $rn,
            'TRANS_MEMO' => $transMemo,
            'hb_fq_num' => $HBFQNum,
            'hb_fq_seller_percent' => $HBFQSellerPercent,
        ];
        $params = ParameterHelper::packValidParameters($params);
        $params && $this->bizContent['extend_params'] = json_encode($params);

        return $this;
    }

    public function setEnablePayChannels(?array $channels): self
    {
        if ($channels) {
            $this->bizContent['enable_pay_channels'] = implode(',', $channels);
        }

        return $this;
    }

    public function setDisablePayChannels(?array $channels): self
    {
        if ($channels) {
            $this->bizContent['disable_pay_channels'] = implode(',', $channels);
        }

        return $this;
    }

    public function setStoreID(?string $id): self
    {
        $this->bizContent['store_id'] = $id;

        return $this;
    }
}