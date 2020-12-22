<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Traits\OpenAPIEnvTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIExtendableTrait;
use Archman\PaymentLib\Alipay\Traits\OpenAPIParameterTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * PC场景下单支付.
 *
 * @see https://opendocs.alipay.com/apis/api_1/alipay.trade.page.pay 文档地址
 */
class TradePagePay implements ParameterMakerInterface
{
    use OpenAPIExtendableTrait;
    use OpenAPIParameterTrait;
    use OpenAPIEnvTrait;

    private const METHOD = 'alipay.trade.page.pay';
    private const VERSION = '1.0';
    private const CHARSET = 'utf-8';

    private OpenAPIConfigInterface $config;

    private array $params = [
        'return_url' => null,
        'notify_url' => null,
    ];

    private array $bizContent = [
        'out_trade_no' => null,
        'product_code' => 'FAST_INSTANT_TRADE_PAY',
        'total_amount' => null,
        'subject' => null,
        'body' => null,
        'time_expire' => null,
        'goods_detail' => null,
        'passback_params' => null,
        'extend_params' => null,
        'goods_type' => null,
        'timeout_express' => null,
        'promo_params' => null,
        'royalty_info' => null,
        'sub_merchant' => null,
        'merchant_order_no' => null,
        'enable_pay_channels' => null,
        'store_id' => null,
        'disable_pay_channels' => null,
        'qr_pay_mode' => null,
        'qrcode_width' => null,
        'settle_info' => null,
        'invoice_info' => null,
        'agreement_sign_params' => null,
        'integration_type' => null,
        'request_from_url' => null,
        'business_params' => null,
        'ext_user_info' => null,
    ];

    public function __construct(OpenAPIConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * 生成已签名的表单HTML.
     *
     * @param bool $autoSubmit 是否在HTML嵌入自动提交的JS代码
     * @param null|string $formID form元素ID,如果不打算自动提交,可以指定form ID以便前端自己掌握提交时间
     *
     * @return string
     * @throws
     */
    public function makeFormHTML(bool $autoSubmit = true, ?string $formID = null): string
    {
        foreach ($this->makeParameters() as $name => $value) {
            $fields[] = "<input name='{$name}' value='{$value}' type='hidden'>";
        }

        $formID = $formID ?? 'TradePagePay_'.md5(sprintf("%d%d", intval(microtime(true) * 1000), random_int(10000, 99999)));
        $submitScript = $autoSubmit ? "<script>document.getElementById('{$formID}').submit();</script>" : '';
        $form = "
            <form id='{$formID}' action='{$this->getBaseUri()}' method='POST' enctype='application/x-www-form-urlencoded'>
                %s
            </form>{$submitScript}";
        $form = sprintf($form, implode("\n", $fields ?? []));

        return $form;
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

    public function setOutTradeNo(?string $no): self
    {
        $this->bizContent['out_trade_no'] = $no;

        return $this;
    }

    public function setTotalAmount(?int $amount): self
    {
        if ($amount !== null) {
            $amount = bcdiv(strval($amount), '100', 2);
        }
        $this->bizContent['total_amount'] = $amount;

        return $this;
    }

    public function setSubject(?string $subject): self
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

    public function setExtendParams(?array $params): self
    {
        $this->bizContent['extend_params'] = $params ? json_encode($params, JSON_THROW_ON_ERROR) : null;

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

    public function setQRPayMode(?string $mode): self
    {
        $this->bizContent['qr_pay_mode'] = $mode;

        return $this;
    }

    public function setQRCodeWidth(?string $width): self
    {
        $this->bizContent['qrcode_width'] = $width;

        return $this;
    }
}