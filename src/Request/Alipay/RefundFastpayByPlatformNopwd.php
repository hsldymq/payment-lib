<?php

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use Archman\PaymentLib\SignatureHelper\Alipay\Generator;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\build_query;

/**
 * 即时到账批量退款无密接口.
 *
 * @see https://os.alipayobjects.com/rmsportal/UYaiBLFsoFZqVgxWkEZx.zip 文档PDF下载地址
 */
class RefundFastpayByPlatformNopwd implements RequestableInterface, ParameterMakerInterface
{
    private const FIXED_SIGN_TYPE = 'MD5';

    private $config;

    /** @var \Datetime */
    private $datetime;

    /** @var string */
    private $serialNumber;

    /** @var array */
    private $detailList = [];

    private $params = [
        'service' => 'refund_fastpay_by_platform_nopwd',
        '_input_charset' => 'utf-8',
        'sign_type' => self::FIXED_SIGN_TYPE,
        'notify_url' => null,
        'dback_notify_url' => null,
        'batch_no' => null,
        'refund_date' => null,
        'batch_num' => null,
        'detail_data' => null,
        'use_freeze_amount' => null,
        'return_type' => 'xml',
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired(
            array_merge($this->params, ['serial_number' => $this->serialNumber]),
            ['detail_data', 'serial_number']
        );
        $parameters = ParameterHelper::packValidParameters($this->params);

        $parameters['partner'] = $this->config->getPartnerID();
        $parameters['batch_no'] = $this->makeBatchNo();
        $parameters['refund_date'] = $this->datetime->format('Y-m-d H:i:s');
        $parameters['batch_num'] = count($this->detailList);
        $parameters['sign'] = (new Generator($this->config, true))->makeSign($parameters, self::FIXED_SIGN_TYPE, ['sign_type']);

        return $parameters;
    }

    public function setNotifyURL(?string $url): self
    {
        $this->params['notify_url'] = $url;

        return $this;
    }

    public function setDbackNotifyURL(?string $url): self
    {
        $this->params['dback_notify_url'] = $url;

        return $this;
    }

    /**
     * 设置流水号(3-24位). 用于生成批次号, 超过24位会截断后面多余的字符.
     *
     * @param string $sn
     *
     * @return self
     */
    public function setSerialNumber(string $sn): self
    {
        $this->serialNumber = mb_substr($sn, 0, 24, 'utf-8');

        return $this;
    }

    /**
     * @param string $tradeNo 支付宝原支付订单号
     * @param int $amount 单位:分
     * @param string $reason 退款原因
     *
     * @return self
     */
    public function addDetailData(string $tradeNo, int $amount, string $reason): self
    {
        $amount = ParameterHelper::transAmountUnit($amount);
        $this->detailList[$tradeNo] = "{$tradeNo}^{$amount}^{$reason}";
        $this->params['detail_data'] = implode('#', $this->detailList);

        return $this;
    }

    public function setUseFreezeAmount(?bool $use): self
    {
        $this->params['use_freeze_amount'] = null;
        is_bool($use) && $this->params['use_freeze_amount'] = $use ? 'Y' : 'N';

        return $this;
    }

    public function setRefundDate(\Datetime $datetime): self
    {
        $this->datetime = $datetime;
        $this->params['refund_date'] = $datetime->format('Y-m-d H:i:s');

        return $this;
    }

    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST','https://mapi.alipay.com/gateway.do?'.build_query($parameters));

        return $request;
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }

    /**
     * 响应回执的数据结构:
     *  1 成功: <alipay><is_success>T</is_success></alipay>
     *  2 失败: <alipay><is_success>F</is_success><error>失败代码</error></alipay>
     *
     * @param ResponseInterface $response
     *
     * @return BaseResponse
     * @throws ErrorResponseException
     */
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $data = DataParser::xmlToArray($response->getBody());

        if (strtoupper($data['is_success']) === 'F') {
            throw new ErrorResponseException($data['error'], $data['error'], $response, $data['error']);
        }

        return new GeneralResponse($data);
    }

    private function makeBatchNo(): string
    {
        return $this->datetime->format('Ymd').$this->serialNumber;
    }
}