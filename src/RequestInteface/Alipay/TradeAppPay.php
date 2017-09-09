<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay;

use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;

/**
 * APP支付.
 * @link https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.4fv1t7&treeId=193&articleId=105465&docType=1 文档地址
 */
class TradeAppPay
{
    use ParametersMakerTrait;

    /** @var AlipayConfig */
    private $config;

    private $biz_content = [
        'body' => null,
        'subject' => null,
        'out_trade_no' => null,
        'timeout_express' => null,
        'total_amount' => null,
        'product_code' => 'QUICK_MSECURITY_PAY', // 必填参数(固定值)
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
    ];

    public function __construct(array $config)
    {
        $this->config = new AlipayConfig($config);
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->biz_content, ['out_trade_no', 'subject', 'total_amount']);

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);
        $parameters = $this->makeSignedParameters(
            'alipay.trade.app.pay',
            $biz_content,
            ['notify_url' => $this->config->getCallbackUrl('pay.app')]
        );

        return $parameters;
    }

    /**
     * 设置商户订单事务号.
     * @param string $out_trade_no
     * @return TradeAppPay
     */
    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->biz_content['out_trade_no'] = $out_trade_no;

        return $this;
    }

    /**
     * 设置支付金额(单位分).
     * @param int $amount
     * @return TradeAppPay
     */
    public function setAmount(int $amount): self
    {
        $this->biz_content['total_amount'] = sprintf('%.2f', $amount / 100);

        return $this;
    }

    /**
     * 设置订单标题.
     * @param string $subject
     * @return TradeAppPay
     */
    public function setSubject(string $subject): self
    {
        $this->biz_content['subject'] = $subject;

        return $this;
    }
}