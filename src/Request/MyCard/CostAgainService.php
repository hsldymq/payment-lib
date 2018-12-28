<?php

namespace Archman\PaymentLib\Request\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;
use Archman\PaymentLib\Request\MyCard\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\MyCard\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\SignatureHelper\MyCard\Generator;

/**
 * 连续扣款.
 */
class CostAgainService implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/CostAgainService';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/CostAgainService';

    private $config;

    private $parameters = [
        'SerialId' => null,
        'FacTradeSeq' => null,
        'Currency' => null,
        'Amount' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, ['SerialId', 'FacTradeSeq', 'Currency', 'Amount']);

        $parameters = ParameterHelper::packValidParameters($this->parameters);
        $parameters['Hash'] = (new Generator($this->config))->makeHash($parameters);

        return $parameters;
    }

    public function setSerialID(string $id): self
    {
        $this->parameters['SerialId'] = $id;

        return $this;
    }

    public function setFacTradeSeq(string $seq): self
    {
        $this->parameters['FacTradeSeq'] = $seq;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->parameters['Currency'] = $currency;

        return $this;
    }

    /**
     * @param int $amount 单位:分
     *
     * @return CostAgainService
     */
    public function setAmount(int $amount): self
    {
        $amount = ParameterHelper::transAmountUnit($amount);

        $this->parameters['Amount'] = $amount;

        return $this;
    }
}