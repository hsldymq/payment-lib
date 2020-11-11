<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\MyCard;

use Archman\PaymentLib\Config\MyCardConfigInterface;
use Archman\PaymentLib\Request\MyCard\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\MyCard\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\MyCard\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * 付费方式和品项代码查询.
 */
class PayItemQuery implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/PayItemQuery';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/PayItemQuery';

    private MyCardConfigInterface $config;

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $parameters = [
            'FacServiceID' => $this->config->getFacServiceID(),
        ];

        return $parameters;
    }
}