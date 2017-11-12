<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

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
 * @link https://os.alipayobjects.com/rmsportal/UYaiBLFsoFZqVgxWkEZx.zip 文档PDF下载地址
 */
class RefundFastpayByPlatformNopwd implements RequestableInterface, ParameterMakerInterface
{
    private const FIXED_SIGN_TYPE = 'MD5';

    private $config;

    /** @var \Datetime */
    private $datetime;

    private $params = [
        'service' => 'refund_fastpay_by_platform_nopwd',
        '_input_charset' => 'utf-8',
        'detail_data' => [],
        'serial_number' => null,
        'dback_notify_url' => null,
        'notify_url' => null,
        'use_freeze_amount' => null,
        'return_type' => 'xml',
        'sign_type' => self::FIXED_SIGN_TYPE,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['detail_data', 'serial_number']);
        $parameters = ParameterHelper::packValidParameters($this->params);

        $datetime = $this->datetime ?? $this->now();
        $parameters['partner'] = $this->config->getPartnerID();
        $parameters['batch_no'] = $this->makeBatchNo($datetime);
        $parameters['refund_date'] = $datetime->format('Y-m-d H:i:s');
        $parameters['batch_num'] = count($this->params['detail_data']);
        $parameters['detail_data'] = implode('#', $this->params['detail_data']);
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, self::FIXED_SIGN_TYPE, ['sign_type']);

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
     * 设置流水号(3-24位). 用于生成批次号.
     * @param string $sn
     * @return self
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
     * @return self
     */
    public function addDetailData(string $trade_no, int $amount, string $reason): self
    {
        $amount = ParameterHelper::transAmountUnit($amount);
        $this->params['detail_data'][$trade_no] = "{$trade_no}^{$amount}^{$reason}";

        return $this;
    }

    public function setUseFreezeAmount(?bool $doesUse): self
    {
        $this->params['use_freeze_amount'] = null;
        is_bool($doesUse) && $this->params['use_freeze_amount'] = $doesUse ? 'Y' : 'N';

        return $this;
    }

    public function setRefundDate(?\Datetime $datetime): self
    {
        $datetime && $this->datetime = $datetime;

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
     * @param ResponseInterface $response
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

    private function now(): string
    {
         return new \DateTime('now', new \DateTimeZone('+0800'));
    }

    private function makeBatchNo(\DateTime $datetime): string
    {
        return $datetime->format('Ymd').$this->params['serial_number'];
    }
}