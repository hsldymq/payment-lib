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
 * 连续扣款取消.
 */
class CostAgainCancel implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/CostAgainCancel';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/CostAgainCancel';

    private MyCardConfigInterface $config;

    private array $parameters = [
        'SerialId' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, ['SerialId']);

        $parameters = ParameterHelper::packValidParameters($this->parameters);
        $parameters['Hash'] = (new Generator($this->config))->makeHash($parameters);

        return $parameters;
    }

    public function setSerialID(string $id): self
    {
        $this->parameters['SerialId'] = $id;

        return $this;
    }
}