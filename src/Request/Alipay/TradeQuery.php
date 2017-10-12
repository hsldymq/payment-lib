<?php
namespace Archman\PaymentLib\RequestInterface\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\DefaultRequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\DefaultResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Alipay\Traits\ParametersMakerTrait;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;

/**
 * 支付宝交易订单查询.
 * @link https://docs.open.alipay.com/api_1/alipay.trade.query 文档地址
 */
class TradeQuery implements RequestableInterface, MutableDateTimeInterface
{
    use DefaultResponseHandlerTrait;
    use DefaultRequestPreparationTrait;
    use ParametersMakerTrait;
    use MutableDateTimeTrait;

    private $config;

    private $sign_type = 'RSA';

    private $response_data_field = 'alipay_trade_query_response';

    private $response_sign_field = 'sign';

    private $biz_content = [
        'trade_no' => null,
        'out_trade_no' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->biz_content, [], ['trade_no', 'out_trade_no']);

        $biz_content = ParameterHelper::packValidParameters($this->biz_content);
        $parameters = $this->makeSignedParameters('alipay.trade.query', $biz_content);

        return $parameters;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->biz_content['out_trade_no'] = $out_trade_no;

        return $this;
    }

    public function setTradeNo(string $trade_no): self
    {
        $this->biz_content['trade_no'] = $trade_no;

        return $this;
    }
}