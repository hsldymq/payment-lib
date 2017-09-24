<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;

/**
 * TODO 有待验证(需要WEB前端配合)
 * 手机网站支付.
 * @link https://docs.open.alipay.com/203/107090/ 文档地址
 */
class TradeWapPay implements MutableDateTimeInterface
{
    use ParametersMakerTrait;
    use MutableDateTimeTrait;

    private $config;

    private $sign_type = 'RSA';

    /** @var string */
    private $return_url = null;

    private $biz_content = [
        'body' => null,
        'subject' => null,                      // 必填
        'out_trade_no' => null,                 // 必填
        'timeout_express' => null,
        'time_expire' => null,
        'total_amount' => null,                 // 必填
        'auth_token' => null,
        'product_code' => 'QUICK_WAP_WAY',      // 必填(固定值: QUICK_WAP_WAY)
        'goods_type' => null,
        'passback_params' => null,
        'promo_params' => [],
        'extend_params' => [
            'sys_service_provider_id' => null,
            'needBuyerRealnamed' => null,
            'TRANS_MEMO' => null,
            'hb_fq_num' => null,
            'hb_fq_seller_percent' => null,
        ],
        'enable_pay_channels' => null,
        'disable_pay_channels' => null,
        'store_id' => null,
        'quit_url' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->biz_content, ['out_trade_no', 'subject', 'total_amount']);

        $extra = ['notify_url' => $this->config->getCallbackUrl('pay.wap')];
        $this->return_url && $extra['return_url'] = $this->return_url;

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);
        $parameters = $this->makeSignedParameters('alipay.trade.wap.pay', $biz_content, $extra);

        return $parameters;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->biz_content['out_trade_no'] = $out_trade_no;

        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->biz_content['subject'] = $subject;

        return $this;
    }

    /**
     * @param int $amount 单位: 分
     * @return TradeWapPay
     */
    public function setTotalAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);

        $this->biz_content['total_amount'] = ParameterHelper::transUnitCentToYuan($amount);

        return $this;
    }

    public function setReturnUrl(string $url): self
    {
        $this->return_url = $url;

        return $this;
    }

    public function setQuitUrl(string $url): self
    {
        $this->biz_content['quit_url'] = $url;

        return $this;
    }
}