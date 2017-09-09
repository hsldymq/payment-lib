<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay;
use Api\Exception\Logic\MakePaymentVendorParametersFailedException;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\SignatureHelper\Alipay\Generator;

/**
 * 批量付款到支付宝账户有密接口.
 * @link https://docs.open.alipay.com/64/104804 文档地址
 */
class BatchTransferNotify
{
    /** @var AlipayConfig */
    private $config;

    private $params = [
        'detail_data' => [],
        'batch_no' => null,
        'extend_param' => null,
    ];

    public function __construct(array $config)
    {
        $this->config = new AlipayConfig($config);
    }

    public function makeTransferUrl(): string
    {
        $parameters = $this->makeParameters();

        return 'https://mapi.alipay.com/gateway.do?'.\GuzzleHttp\Psr7\build_query($parameters);
    }

    public function makeParameters(): array
    {
        $now = \get_now_datetime();
        $parameters = [
            'service'        => 'batch_trans_notify',
            'partner'        => $this->config->getPartnerID(),
            '_input_charset' => 'utf-8',
            'sign_type'      => 'MD5',
            'notify_url'     => $this->config->getCallbackUrl('transfer.batch'),
            'account_name'   => $this->config->getAccountName(),
            'detail_data'    => $this->makeDetailDataString(),
            'batch_no'       => $this->params['batch_no'],
            'batch_num'      => count($this->params['detail_data']),
            'batch_fee'      => $this->calcBatchFee(),
            'email'          => $this->config->getAccountEmail(),
            'pay_date'       => $now->format('Ymd'),
        ];
        $this->params['extend_param'] && $parameters['extend_param'] = $this->params['extend_param'];

        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, 'MD5');
        ksort($parameters);

        return $parameters;
    }

    public function setBatchNo(string $batch_no): self
    {
        if (!preg_match('/^[a-z0-9]{11-32}$/i', $batch_no)) {
            throw new MakePaymentVendorParametersFailedException([
                'message' => 'Length of The Batch No Should Be 11 <= x <= 32, And Should Be a Combination Of Alphanumeric'
            ]);
        }

        $this->params['batch_no'] = $batch_no;

        return $this;
    }

    /**
     * 增加转账数据.
     * @param string $out_trade_no 商户订单号
     * @param string $user_account 转账目标用户账号
     * @param string $user_real_name 用户姓名
     * @param int $amount 金额(单位:分)
     * @param string $remark 备注
     * @return BatchTransferNotify
     * @throws MakePaymentVendorParametersFailedException
     */
    public function addDetailData(
        string $out_trade_no,
        string $user_account,
        string $user_real_name,
        int $amount,
        string $remark
    ): self {
        ParameterHelper::checkAmount($amount);

        if (strlen($out_trade_no) > 64) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of out_trade_no Is Too Long']);
        }

        if (strlen($user_account) >= 100) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of user_account Is Too Long']);
        }

        if (strlen($remark) > 200) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of remark Is Too Long']);
        }

        $this->params['detail_data'][] = [
            'out_trade_no' => $out_trade_no,
            'user_account' => $user_account,
            'user_real_name' => $user_real_name,
            'amount' => $amount,
            'remark' => $remark,
        ];

        return $this;
    }

    public function addExtendParam(string $name, string $value): self
    {
        // 退款理由不能包含^,|等特殊字符
        if (preg_match('/\^|\|/', $name) || preg_match('/\^|\|/', $value)) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Extend Param Should Not Include Special Characters']);
        }

        if ($this->params['extend_param'] !== null) {
            $this->params['extend_param'] .= "|{$name}^{$value}";
        } else {
            $this->params['extend_param'] = "{$name}^{$value}";
        }

        return $this;
    }

    private function makeDetailDataString(): string
    {
        $list = [];
        foreach ($this->params['detail_data'] as $each) {
            $amount = sprintf('%.2f', $each['amount'] / 100);
            $list[] = "{$each['out_trade_no']}^{$each['user_account']}^{$each['user_real_name']}^{$amount}^{$each['remark']}";
        }

        return implode('|', $list);
    }

    private function calcBatchFee(): string
    {
        $total_amount = 0;

        foreach ($this->params['detail_data'] as $each) {
            $total_amount += $each['amount'];
        }

        return sprintf('%.2f', $total_amount / 100);
    }
}