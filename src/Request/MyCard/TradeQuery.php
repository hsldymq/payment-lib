<?php

namespace Archman\PaymentLib\Request\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;
use Archman\PaymentLib\Request\MyCard\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\MyCard\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 验证MyCard交易结果.
 */
class TradeQuery implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/TradeQuery';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/TradeQuery';

    private $config;

    private $parameters = [
        'AuthCode' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, ['AuthCode']);

        return $this->parameters;
    }

    public function setAuthCode(string $code): self
    {
        $this->parameters['AuthCode'] = $code;

        return $this;
    }
}