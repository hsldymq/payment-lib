<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;
use Archman\PaymentLib\Request\MyCard\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\MyCard\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\MyCard\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\SignatureHelper\MyCard\Generator;

/**
 * 卡号密码储值.
 */
class IngamePay implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/IngamePay';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/IngamePay';

    private MyCardConfigInterface $config;

    private array $parameters = [
        'AuthCode' => null,
        'CardID' => null,
        'CardPW' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, ['AuthCode', 'CardID', 'CardPW']);

        $parameters = ParameterHelper::packValidParameters($this->parameters);
        $parameters['Hash'] = (new Generator($this->config))->makeHash($parameters);

        return $parameters;
    }

    public function setAuthCode(string $code): self
    {
        $this->parameters['AuthCode'] = $code;

        return $this;
    }

    public function setCardID(string $id): self
    {
        $this->parameters['CardID'] = $id;

        return $this;
    }

    public function setCardPW(string $pw): self
    {
        $this->parameters['CardPW'] = $pw;

        return $this;
    }
}