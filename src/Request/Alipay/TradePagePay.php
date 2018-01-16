<?php
namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * PC场景下单支付.
 * @link https://docs.open.alipay.com/270/alipay.trade.page.pay
 */
class TradePagePay implements ParameterMakerInterface
{
    use OpenAPIParameterMakerTrait;

    private $config;

    private $params = [
        'return_url' => null,
        'notify_url' => null,
    ];

    private $bizContent = [
        'out_trade_no' => null,
        'product_code' => 'FAST_INSTANT_TRADE_PAY', // 必填参数(固定值)
        'total_amount' => null,
        'subject' => null,
        'body' => null,
        'goods_detail' => null,
        'passback_params' => null,
        'extend_params' => null,
        'goods_type' => null,
        'timeout_express' => null,
        'enable_pay_channels' => null,
        'disable_pay_channels' => null,
        'auth_token' => null,
        'qr_pay_mode' => null,
        'qrcode_width' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * 生成已签名的表单HTML.
     * @param bool $autoSubmit 是否在HTML嵌入自动提交的JS代码
     * @param null|string $formID form元素ID,如果不打算自动提交,可以指定form ID以便前端自己掌握提交时间
     * @return string
     */
    public function makeFormHTML(bool $autoSubmit = true, ?string $formID = null): string
    {
        foreach ($this->makeParameters() as $name => $value) {
            $fields[] = "<input name='{$name}' value='{$value}' type='hidden'>";
        }

        $formID = $formID ?? 'TradePagePay_'.md5(intval(microtime(true) * 1000).random_int(10000, 99999));
        $submitScript = $autoSubmit ? "<script>document.getElementById('{$formID}').submit();</script>" : '';
        $form = "
            <form id='{$formID}' action='https://openapi.alipay.com/gateway.do' method='POST' enctype='application/x-www-form-urlencoded'>
                %s
                {$submitScript}
            </form>";
        $form = sprintf($form, implode('', $fields ?? []));

        return $form;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_trade_no', 'subject', 'total_amount']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeSignedParameters('alipay.trade.page.pay', $bizContent);

        return $parameters;
    }

    public function setReturnURL(?string $url): self
    {
        $this->params['return_url'] = $url;

        return $this;
    }

    public function setNotifyURL(?string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->bizContent['out_trade_no'] = $no;

        return $this;
    }

    public function setTotalAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->bizContent['total_amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->bizContent['subject'] = $subject;

        return $this;
    }

    public function setBody(?string $body): self
    {
        $this->bizContent['body'] = $body;

        return $this;
    }

    public function setGoodsDetail(?string $showURL): self
    {
        $data = ParameterHelper::packValidParameters(['show_url' => $showURL]);
        $this->bizContent['goods_detail'] = $data ? json_encode($data) : null;

        return $this;
    }

    public function setPassbackParams(?string $params): self
    {
        $this->bizContent['passback_params'] = $params;

        return $this;
    }

    public function setExtendParams(
        ?string $sysServiceProviderID = null,
        ?int $HBFQNum = null,
        ?float $HBFQSellerPercent = null
    ): self {
        $data = ParameterHelper::packValidParameters([
            'sys_service_provider_id' => $sysServiceProviderID,
            'hb_fq_num' => "$HBFQNum",
            'hb_fq_seller_percent' => "$HBFQSellerPercent",
        ]);
        $this->bizContent['extend_params'] = $data ? json_encode($data) : null;

        return $this;
    }

    public function setGoodsType(?string $type): self
    {
        $this->bizContent['goods_type'] = $type;

        return $this;
    }

    public function setTimeoutExpress(?int $minutes): self
    {
        $this->bizContent['timeout_express'] = is_null($minutes) ? null : "{$minutes}m";

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

    public function setAuthToken(?string $token): self
    {
        $this->bizContent['auth_token'] = $token;

        return $this;
    }

    public function setQRPayMode(?int $mode): self
    {
        $this->bizContent['qr_pay_mode'] = "$mode";

        return $this;
    }

    public function setQRCodeWidth(?int $width): self
    {
        $this->bizContent['qrcode_width'] = "$width";

        return $this;
    }
}