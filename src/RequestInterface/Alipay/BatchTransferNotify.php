<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;

/**
 * // TODO 有待验证(跳到支付宝转账界面,明细提示验证成功,但是系统不支持,无法显示密码表单)
 * 批量付款到支付宝账户有密接口.
 * @link https://docs.open.alipay.com/64/104804 文档地址
 * @link https://docs.open.alipay.com/common/104741 生成签名的方式(需要剔除掉sign_type)
 */
class BatchTransferNotify implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

    private $config;

    private $params = [
        'detail_data' => [],            // 必填
        'batch_no' => null,             // 必填
        'extend_param' => null,
    ];

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
        ParameterHelper::checkRequired($this->params, ['batch_no', 'detail_data']);

        $now = $this->getDateTime();
        $parameters = [
            'service'        => 'batch_trans_notify',
            'partner'        => $this->config->getPartnerID(),
            '_input_charset' => 'utf-8',
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
        $parameters['sign_type'] = 'MD5';
        ksort($parameters);

        return $parameters;
    }

    public function setBatchNo(string $batch_no): self
    {
        if (!preg_match('/^[a-z0-9]{11,32}$/i', $batch_no)) {
            throw new MakePaymentVendorParametersFailedException([
                'message' => 'Length of The Batch No Should Be 11 <= x <= 32, And Should Be a Combination Of Alphanumeric'
            ]);
        }

        $this->params['batch_no'] = $batch_no;

        return $this;
    }

    /**
     * 增加转账数据.
     * @param string $serial_no 商户订单号
     * @param string $user_account 转账目标用户账号
     * @param string $user_real_name 用户姓名
     * @param int $amount 金额(单位:分)
     * @param string $remark 备注
     * @return BatchTransferNotify
     * @throws MakePaymentVendorParametersFailedException
     */
    public function addDetailData(
        string $serial_no,
        string $user_account,
        string $user_real_name,
        int $amount,
        string $remark
    ): self {
        ParameterHelper::checkAmount($amount);

        if (strlen($serial_no) > 64) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of out_trade_no Is Too Long']);
        }

        if (strlen($user_account) >= 100) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of user_account Is Too Long']);
        }

        if (strlen($remark) > 200) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Length Of remark Is Too Long']);
        }

        $this->params['detail_data'][] = [
            'serial_no' => $serial_no,
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
            $amount = ParameterHelper::transUnitCentToYuan($each['amount']);
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

        return ParameterHelper::transUnitCentToYuan($total_amount);
    }
}