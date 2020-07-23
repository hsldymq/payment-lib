<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 单笔转账到支付宝账户接口.
 *
 * @link https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer/
 */
class FundTransToAccountTransfer implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use OpenAPIParameterMakerTrait;
    use DefaultSenderTrait;

    public const PAYEE_TYPE_LOGONID = 'ALIPAY_LOGONID';
    public const PAYEE_TYPE_USERID = 'ALIPAY_USERID';

    private const SIGN_FIELD = 'sign';
    private const CONTENT_FIELD = 'alipay_fund_trans_toaccount_transfer_response';

    private AlipayConfigInterface $config;

    private array $bizContent = [
        'out_biz_no' => null,           // 必填
        'payee_type' => null,           // 必填
        'payee_account' => null,        // 必填
        'amount' => null,               // 必填
        'payer_show_name' => null,
        'payee_real_name' => null,
        'remark' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_biz_no', 'payee_type', 'payee_account', 'amount']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);

        $parameters = $this->makeSignedParameters('alipay.fund.trans.toaccount.transfer', $bizContent);

        return $parameters;
    }

    public function setOutBizNo(string $outBizNo): self
    {
        $this->bizContent['out_biz_no'] = $outBizNo;

        return $this;
    }

    public function setPayeeType(string $type)
    {
        $this->bizContent['payee_type'] = $type;

        return $this;
    }

    public function setPayeeAccount(string $account): self
    {
        $this->bizContent['payee_account'] = $account;

        return $this;
    }

    /**
     * 设置金额.
     *
     * @param int $amount 单位: 分
     *
     * @return FundTransToAccountTransfer
     * @throws
     */
    public function setAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->bizContent['amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setPayerShowName(?string $name): self
    {
        $this->bizContent['payer_show_name'] = $name;

        return $this;
    }

    public function setPayeeRealName(?string $name): self
    {
        $this->bizContent['payee_real_name'] = $name;

        return $this;
    }

    public function setRemark(?string $remark): self
    {
        $this->bizContent['remark'] = $remark;

        return $this;
    }
}