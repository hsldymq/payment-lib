<?php
namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 统一收单交易撤销接口.
 * @reference https://docs.open.alipay.com/api_1/alipay.trade.cancel/
 */
class TradeCancel implements RequestableInterface
{
    use OpenAPIResponseHandlerTrait;
    use OpenAPIRequestPreparationTrait;
    use OpenAPIParameterMakerTrait;

    private const SIGN_FIELD = 'sign';

    private const CONTENT_FIELD = 'alipay_trade_cancel_response';

    private $config;

    private $params = [
        'app_auth_token' => null,
    ];

    private $bizContent = [
        'trade_no' => null,
        'out_trade_no' => null,
    ];

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->bizContent, [], ['trade_no', 'out_trade_no']);

        $bizContent = ParameterHelper::packValidParameters($this->bizContent);
        $parameters = $this->makeSignedParameters('alipay.trade.cancel', $bizContent);

        return $parameters;
    }

    public function setAppAuthToken(?string $token): self
    {
        $this->params['app_auth_token'] = $token;

        return $this;
    }

    public function setTradeNo(?string $trade_no): self
    {
        $this->bizContent['trade_no'] = $trade_no;

        return $this;
    }

    public function setOutTradeNo(?string $out_trade_no): self
    {
        $this->bizContent['out_trade_no'] = $out_trade_no;

        return $this;
    }
}