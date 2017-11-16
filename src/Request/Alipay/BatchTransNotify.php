<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\SignatureHelper\Alipay\Generator;

/**
 * 批量付款到支付宝账户有密接口.
 * @link https://docs.open.alipay.com/64/104804
 * @link https://docs.open.alipay.com/common/104741 sign_type不参与签名
 */
class BatchTransNotify implements ParameterMakerInterface
{
    private $config;

    private $params = [
        'service' => 'batch_trans_notify',
        '_input_charset' => 'utf-8',
        'notify_url' => null,
        'account_name' => null,         // 必填
        'detail_data' => null,          // 必填
        'batch_no' => null,             // 必填
        'email' => null,                // 必填
        'buyer_account_name' => null,
        'extend_param' => null,
    ];

    /** @var \Datetime */
    private $payDate;

    private $detailDataArr = [];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeTransferUrl(): string
    {
        $parameters = $this->makeParameters();

        return 'https://mapi.alipay.com/gateway.do?'.\GuzzleHttp\Psr7\build_query($parameters);
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['account_name', 'batch_no', 'detail_data', 'email']);

        $signType = $this->config->getMAPIDefaultSignType();
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['partner'] = $this->config->getPartnerID();
        $parameters['sign_type'] = $signType;
        $parameters['batch_num'] = count($this->params['detail_data']);
        $parameters['batch_fee'] = $this->calcBatchFee();
        $parameters['pay_date'] = $this->getDatetime()->format('ymd');
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType, ['sign_type']);

        ksort($parameters);

        return $parameters;
    }

    public function setNotifyURL(?string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setBatchNo(string $batch_no): self
    {
        $this->params['batch_no'] = $batch_no;

        return $this;
    }

    public function setAccountName(string $name): self
    {
        $this->params['account_name'] = $name;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->params['email'] = $email;

        return $this;
    }

    /**
     * 增加转账数据.
     * @param string $serial_no 商户订单号
     * @param string $user_account 转账目标用户账号
     * @param string $user_real_name 用户姓名
     * @param int $amount 金额(单位:分)
     * @param string $remark 备注
     * @return BatchTransNotify
     */
    public function addDetailData(
        string $serial_no,
        string $user_account,
        string $user_real_name,
        int $amount,
        string $remark
    ): self {
        ParameterHelper::checkAmount($amount);

        $this->detailDataArr[$serial_no] = [
            'serial_no' => $serial_no,
            'user_account' => $user_account,
            'user_real_name' => $user_real_name,
            'amount' => $amount,
            'remark' => $remark,
        ];

        $this->params['detail_data'] = $this->makeDetailDataString();

        return $this;
    }

    public function setBuyerAccountName(?string $name): self
    {
        $this->params['buyer_account_name'] = $name;

        return $this;
    }

    public function addExtendParam(string $name, string $value): self
    {
        if ($this->params['extend_param'] !== null) {
            $this->params['extend_param'] .= "|{$name}^{$value}";
        } else {
            $this->params['extend_param'] = "{$name}^{$value}";
        }

        return $this;
    }

    public function setPayDate(\Datetime $dt): self
    {
        $this->payDate = $dt;
    }

    protected function getDatetime(): \Datetime
    {
        return $this->payDate instanceof \Datetime ? $this->payDate : new \Datetime('now');
    }

    private function makeDetailDataString(): string
    {
        $list = [];
        foreach ($this->detailDataArr as $each) {
            $amount = ParameterHelper::transAmountUnit($each['amount']);
            $list[] = "{$each['serial_no']}^{$each['user_account']}^{$each['user_real_name']}^{$amount}^{$each['remark']}";
        }

        return implode('|', $list);
    }

    private function calcBatchFee(): string
    {
        $total_amount = 0;

        foreach ($this->params['detail_data'] as $each) {
            $total_amount += $each['amount'];
        }

        return ParameterHelper::transAmountUnit($total_amount);
    }
}