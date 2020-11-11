<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\Config\AlipayConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\SignatureHelper\Alipay\Generator;

/**
 * 批量付款到支付宝账户有密接口.
 *
 * @link https://docs.open.alipay.com/64/104804
 * @link https://docs.open.alipay.com/common/104741 sign_type不参与签名
 */
class BatchTransNotify implements ParameterMakerInterface
{
    private AlipayConfigInterface $config;

    private array $params = [
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

    private \DateTime $payDate;

    private array $detailDataArr = [];

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
        $parameters['batch_num'] = count($this->detailDataArr);
        $parameters['batch_fee'] = $this->calcBatchFee();
        $parameters['pay_date'] = $this->getDatetime()->format('Ymd');
        $parameters['sign'] = (new Generator($this->config, true))->makeSign($parameters, $signType, ['sign_type']);

        ksort($parameters);

        return $parameters;
    }

    public function setNotifyURL(?string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setAccountName(string $name): self
    {
        $this->params['account_name'] = $name;

        return $this;
    }

    /**
     * 增加转账数据.
     *
     * @param string $serialNo 商户订单号
     * @param string $userAccount 转账目标用户账号
     * @param string $userRealName 用户姓名
     * @param int $amount 金额(单位:分)
     * @param string $remark 备注
     *
     * @return BatchTransNotify
     * @throws
     */
    public function addDetailData(
        string $serialNo,
        string $userAccount,
        string $userRealName,
        int $amount,
        string $remark
    ): self {
        ParameterHelper::checkAmount($amount);

        $this->detailDataArr[$serialNo] = [
            'serial_no' => $serialNo,
            'user_account' => $userAccount,
            'user_real_name' => $userRealName,
            'amount' => $amount,
            'remark' => $remark,
        ];

        $this->params['detail_data'] = $this->makeDetailDataString();

        return $this;
    }

    public function setBatchNo(string $batchNo): self
    {
        $this->params['batch_no'] = $batchNo;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->params['email'] = $email;

        return $this;
    }

    public function setPayDate(\Datetime $dt): self
    {
        $this->payDate = $dt;

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
        $totalAmount = 0;

        foreach ($this->detailDataArr as $each) {
            $totalAmount += $each['amount'];
        }

        return ParameterHelper::transAmountUnit($totalAmount);
    }
}