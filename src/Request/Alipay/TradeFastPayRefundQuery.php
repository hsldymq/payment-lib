<?php
namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 统一收单交易退款查询.
 * @link https://docs.open.alipay.com/api_1/alipay.trade.fastpay.refund.query/
 */
class TradeFastPayRefundQuery implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIParameterMakerTrait;
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;

    private const SIGN_FIELD = 'sign';

    private const CONTENT_FIELD = 'alipay_trade_fastpay_refund_query_response';

    /** @var AlipayConfigInterface */
    private $config;

    private $params = [
        'app_auth_token' => null,
    ];

    private $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
        'out_request_no' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, ['out_request_no'], ['trade_no', 'out_trade_no']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeSignedParameters('alipay.trade.fastpay.refund.query', $bizContent);

        return $parameters;
    }

    public function setAppAuthToken(?string $token): self
    {
        $this->params['app_auth_token'] = $token;

        return $this;
    }

    public function setTradeNo(?string $no): self
    {
        $this->bizContent['trade_no'] = $no;

        return $this;
    }

    public function setOutTradeNo(?string $no): self
    {
        $this->bizContent['out_trade_no'] = $no;

        return $this;
    }

    public function setOutRequestNo(string $no): self
    {
        $this->bizContent['out_request_no'] = $no;

        return $this;
    }
}