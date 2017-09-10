<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay;

use Api\Exception\Logic\MakePaymentVendorParametersFailedException;
use Api\Exception\Logic\VendorInterfaceResponseErrorException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\DataParser;
use Utils\PaymentVendor\ErrorMapper\Alipay;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\CertVerificationLessTrait;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;
use Utils\PaymentVendor\SignatureHelper\Alipay\Generator;
use function GuzzleHttp\Psr7\build_query;

/**
 * @link https://os.alipayobjects.com/rmsportal/UYaiBLFsoFZqVgxWkEZx.zip 文档PDF下载地址
 */
class NoPasswordBatchRefund implements RequestableInterface, MutableDateTimeInterface
{
    use CertVerificationLessTrait;
    use MutableDateTimeTrait;

    private $config;

    private $params = [
        'detail_data' => [],
        'serial_number' => null,
        'dback_notify_url' => null,
    ];

    public function __construct(AlipayConfig $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['detail_data', 'serial_number']);

        $now = $this->getDateTime();
        $parameters = [
            'service'           => 'refund_fastpay_by_platform_nopwd',
            'partner'           => $this->config->getPartnerID(),
            '_input_charset'    => 'utf-8',
            'notify_url'        => $this->config->getCallbackUrl('refund.batch'),
            'batch_no'          => $this->makeBatchNo($now),
            'refund_date'       => $now->format('Y-m-d H:i:s'),
            'batch_num'         => count($this->params['detail_data']),
            'detail_data'       => implode('#', $this->params['detail_data']),
            'use_freeze_amount' => 'N',
            'return_type'       => 'xml',
        ];
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, 'MD5');
        $parameters['sign_type'] = 'MD5';

        return $parameters;
    }

    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST','https://mapi.alipay.com/gateway.do?'.build_query($parameters));

        return $request;
    }

    /**
     * 响应回执的数据结构:
     *  1 成功: <alipay><is_success>T</is_success></alipay>
     *  2 失败: <alipay><is_success>F</is_success><error>失败代码</error></alipay>
     * @param ResponseInterface $response
     * @return array
     * @throws VendorInterfaceResponseErrorException
     */
    public function handleResponse(ResponseInterface $response): array
    {
        $data = DataParser::parseXML($response->getBody());

        if (strtoupper($data['is_success']) === 'F') {
            $error = Alipay::map($data['error']);
            throw new VendorInterfaceResponseErrorException($data['error'], $data, [
                'message' => "Request Alipay Batch Refund Interface Error, Failed Code: {$error['code']}, Failed Text: {$error['text']}"
            ]);
        }

        return $data;
    }

    /**
     * 设置流水号(3-24位). 用于生成批次号.
     * @param string $sn
     * @return NoPasswordBatchRefund
     */
    public function setSerialNumber(string $sn): self
    {
        $this->params['serial_number'] = mb_substr($sn, 0, 24, 'utf-8');

        return $this;
    }

    /**
     * @param string $trade_no 支付宝原支付订单号
     * @param int $amount 单位:分
     * @param string $reason 退款原因
     * @return NoPasswordBatchRefund
     * @throws MakePaymentVendorParametersFailedException
     */
    public function addDetailData(string $trade_no, int $amount, string $reason): self
    {
        // 退款理由不能包含^,|,$,#等特殊字符
        if (preg_match('/\^|\||\$|\#/', $reason) || preg_match('/\^|\||\$|\#/', $trade_no)) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Refund Detail Data Should Not Include Special Characters.']);
        }

        if (isset($this->params['detail_data'][$trade_no])) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Duplicated Trade No In Same Batch.']);
        }

        $amount = ParameterHelper::transUnitCentToYuan($amount);
        $this->params['detail_data'][$trade_no] = "{$trade_no}^{$amount}^{$reason}";

        return $this;
    }

    private function makeBatchNo(\DateTime $datetime): string
    {
        return $datetime->format('Ymd').$this->params['serial_number'];
    }
}