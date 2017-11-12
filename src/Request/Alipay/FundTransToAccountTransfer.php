<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\RequestInterface\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 单笔转账到支付宝账户接口.
 * @link https://docs.open.alipay.com/api_28/alipay.fund.trans.toaccount.transfer/ 文档地址
 */
class FundTransToAccountTransfer implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use ParametersMakerTrait;

    private $config;

    private $signType = 'RSA';

    private $response_data_field = 'alipay_fund_trans_toaccount_transfer_response';

    private $response_sign_field = 'sign';

    private $biz_content = [
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
        ParameterHelper::checkRequired($this->biz_content, ['out_biz_no', 'payee_type', 'payee_account', 'amount']);

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);

        $parameters = $this->makeOpenAPISignedParameters('alipay.fund.trans.toaccount.transfer', $biz_content);

        return $parameters;
    }

    public function setOutBizNo(string $out_biz_no): self
    {
        $this->biz_content['out_biz_no'] = $out_biz_no;

        return $this;
    }

    public function setPayeeType(bool $byLogonID)
    {
        $this->biz_content['payee_type'] = $byLogonID ? 'ALIPAY_LOGONID' : 'ALIPAY_USERID';

        return $this;
    }

    public function setPayeeAccount(string $payee_account): self
    {
        $this->biz_content['payee_account'] = $payee_account;

        return $this;
    }

    public function setAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->biz_content['amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setPayerShowName(string $name): self
    {
        $this->biz_content['payer_show_name'] = $name;

        return $this;
    }

    public function setPayeeRealName(string $real_name): self
    {
        $this->biz_content['payee_real_name'] = $real_name;

        return $this;
    }

    public function setRemark(string $remark): self
    {
        $this->biz_content['remark'] = $remark;

        return $this;
    }
}