<?php

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;

/**
 * 手机网站支付.
 *
 * @see https://docs.open.alipay.com/203/107090/ 文档地址
 */
class TradeWapPay implements ParameterMakerInterface
{
    use OpenAPIParameterMakerTrait;

    private $config;

    private $params = [
        'return_url' => null,
        'notify_url' => null,
    ];

    private $bizContent = [
        'body' => null,
        'subject' => null,
        'out_trade_no' => null,
        'timeout_express' => null,
        'time_expire' => null,
        'total_amount' => null,
        'auth_token' => null,
        'product_code' => 'QUICK_WAP_WAY',
        'goods_type' => null,
        'passback_params' => null,
        'promo_params' => null,
        'extend_params' => null,
        'enable_pay_channels' => null,
        'disable_pay_channels' => null,
        'store_id' => null,
        'quit_url' => null,
        'ext_user_info' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
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

        $formID = $formID ?? 'TradeWapPay_'.md5(intval(microtime(true) * 1000).random_int(10000, 99999));
        $submitScript = $autoSubmit ? "<script>document.getElementById('{$formID}').submit();</script>" : '';
        $form = "
            <form id='{$formID}' action='https://openapi.alipay.com/gateway.do' method='POST' enctype='application/x-www-form-urlencoded'>
                %s
            </form>{$submitScript}";

        $indent = str_pad("", 4 * 4, " ");
        $form = sprintf($form, implode("\n{$indent}", $fields ?? []));

        return $form;
    }

    /**
     * @return array
     * @throws
     */
    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_trade_no', 'subject', 'total_amount']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeSignedParameters('alipay.trade.wap.pay', $bizContent);

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

    public function setOutTradeNo(string $no): self
    {
        $this->bizContent['out_trade_no'] = $no;

        return $this;
    }

    public function setTimeoutExpress(?int $minutes): self
    {
        $this->bizContent['timeout_express'] = is_null($minutes) ? null : "{$minutes}m";

        return $this;
    }

    public function setTimeExpire(?\DateTime $dt): self
    {
        $this->bizContent['time_expire'] = $dt->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @param int $amount 单位: 分
     * @return self
     */
    public function setTotalAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->bizContent['total_amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setAuthToken(?string $token): self
    {
        $this->bizContent['auth_token'] = $token;

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
        ?string $sysServiceProviderID = null,
        ?bool $needBuyerRealNamed = null,
        ?string $transMemo = null,
        ?int $HBFQNum = null,
        ?float $HBFQSellerPercent = null
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

    public function setQuitUrl(?string $url): self
    {
        $this->bizContent['quit_url'] = $url;

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