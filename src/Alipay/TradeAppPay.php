<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * app支付接口.
 *
 * @see https://opendocs.alipay.com/apis/api_1/alipay.trade.app.pay
 */
class TradeAppPay implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;

    private const METHOD = 'alipay.trade.app.pay';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';
    private const WITH_CERT = false;

    private OpenAPIConfigInterface $config;

    private array $params = [
        'timestamp' => null,
        'notify_url' => null,
    ];

    private array $bizContent = [
        'subject' => null,
        'out_trade_no' => null,
        'timeout_express' => null,
        'total_amount' => null,
        'product_code' => 'QUICK_MSECURITY_PAY',
        'body' => null,
        'time_expire' => null,
        'goods_type' => null,
        'promo_params' => null,
        'passback_params' => null,
        'extend_params' => null,
        'merchant_order_no' => null,
        'enable_pay_channels' => null,
        'store_id' => null,
        'specified_channel' => null,
        'disable_pay_channels' => null,
        'goods_detail' => null,
        'ext_user_info' => null,
        'business_params' => null,
        'agreement_sign_params' => null,
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

    /**
     * 设置该笔订单允许的最晚付款时间.
     *
     * @param int|null $minutes 统一使用分钟来表示
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
     * @param int|null $amount
     *
     * @return self
     */
    public function setTotalAmount(?int $amount): self
    {
        if ($amount !== null) {
            $amount = bcdiv(strval($amount), '100', 2);
        }
        $this->bizContent['total_amount'] = $amount;

        return $this;
    }

    /**
     * 设置销售产品码.
     *
     * 不设置默认为QUICK_MSECURITY_PAY.
     *
     * @param string|null $code
     *
     * @return $this
     */
    public function setProductCode(?string $code): self
    {
        $this->bizContent['product_code'] = $code ?? 'QUICK_MSECURITY_PAY';

        return $this;
    }

    /**
     * 设置交易描述信息.
     *
     * @param string|null $body
     *
     * @return $this
     */
    public function setBody(?string $body): self
    {
        $this->bizContent['body'] = $body;

        return $this;
    }

    /**
     * 设置标题.
     *
     * @param string|null $subject
     *
     * @return $this
     */
    public function setSubject(?string $subject): self
    {
        $this->bizContent['subject'] = $subject;

        return $this;
    }

    /**
     * 设置商户唯一订单号.
     *
     * @param string|null $tradeNo
     *
     * @return $this
     */
    public function setOutTradeNo(?string $tradeNo): self
    {
        $this->bizContent['out_trade_no'] = $tradeNo;

        return $this;
    }

    /**
     * 设置绝对超时时间.
     *
     * @param \DateTimeInterface|null $dt 需要保证其时区为东八区
     *
     * @return $this
     */
    public function setTimeExpire(?\DateTimeInterface $dt): self
    {
        if (!$dt) {
            $this->bizContent['time_expire'] = null;
            return $this;
        }

        $this->bizContent['time_expire'] = (new \DateTime("@{$dt->getTimestamp()}"))
            ->setTimezone(new \DateTimeZone('+0800'))
            ->format('Y-m-d H:i');

        return $this;
    }

    /**
     * 设置商品主类型.
     *
     * @param string|null $type
     *
     * @return $this
     */
    public function setGoodsType(?string $type): self
    {
        $this->bizContent['goods_type'] = $type;

        return $this;
    }

    public function setPromoParams(?string $params): self
    {
        $this->bizContent['promo_params'] = $params;

        return $this;
    }

    public function setPassbackParams(?string $params): self
    {
        $this->bizContent['passback_params'] = $params;

        return $this;
    }

    public function setExtendParams(?array $params): self
    {
        $this->bizContent['extend_params'] = $params ? json_encode($params, JSON_THROW_ON_ERROR) : null;

        return $this;
    }

    /**
     * 设置商户原始订单号.
     *
     * @param string|null $no
     *
     * @return $this
     */
    public function setMerchantOrderNo(?string $no): self
    {
        $this->bizContent['merchant_order_no'] = $no;

        return $this;
    }

    public function setEnablePayChannels(?array $channels): self
    {
        $this->bizContent['enable_pay_channels'] = implode(',', $channels ?? []) ?: null;

        return $this;
    }

    public function setStoreID(?string $id): self
    {
        $this->bizContent['store_id'] = $id;

        return $this;
    }

    public function SpecifiedChannel(?string $channel): self
    {
        $this->bizContent['specified_channel'] = $channel;

        return $this;
    }

    public function setDisablePayChannels(?array $channels): self
    {
        $this->bizContent['disable_pay_channels'] = implode(',', $channels ?? []) ?: null;

        return $this;
    }

    public function setGoodsDetail(?array $detail): self
    {
        $this->bizContent['goods_detail'] = $detail ? json_encode($detail, JSON_THROW_ON_ERROR) : null;

        return $this;
    }

    public function setExtUserInfo(?array $info): self
    {
        $this->bizContent['ext_user_info'] = $info ? json_encode($info, JSON_THROW_ON_ERROR) : null;

        return $this;
    }

    public function setBusinessParams(?string $params): self
    {
        $this->bizContent['business_params'] = $params;

        return $this;
    }

    public function setAgreementSignParams(?array $params): self
    {
        $this->bizContent['agreement_sign_params'] = $params ? json_encode($params, JSON_THROW_ON_ERROR) : null;

        return $this;
    }
}