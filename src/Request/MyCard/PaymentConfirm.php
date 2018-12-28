<?php

namespace Archman\PaymentLib\Request\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;
use Archman\PaymentLib\Request\MyCard\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\MyCard\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 确认MyCard交易.
 */
class PaymentConfirm implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/PaymentConfirm';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/PaymentConfirm';

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