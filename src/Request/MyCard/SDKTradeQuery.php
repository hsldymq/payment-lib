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
 * 查询交易状态.
 */
class SDKTradeQuery implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private const TEST_URI = 'https://testb2b.mycard520.com.tw/MyBillingPay/v1.1/SDKTradeQuery';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/SDKTradeQuery';

    private MyCardConfigInterface $config;

    private array $parameters = [
        'FacServiceId' => null,
        'FacTradeSeq' => null,
        'StartDateTime' => null,
        'EndDateTime' => null,
        'CancelStatus' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
        $this->parameters['FacServiceId'] = $this->config->getFacServiceID();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, ['CancelStatus']);

        $parameters = $this->parameters;
        $parameters['Hash'] = (new Generator($this->config))->makeHash($parameters);

        return $parameters;
    }

    public function setFacTradeSeq(?string $seq): self
    {
        $this->parameters['FacTradeSeq'] = $seq;

        return $this;
    }

    public function setStartDateTime(?\DateTime $dt): self
    {
        $this->parameters['StartDateTime'] = $dt === null ? null : $dt->format('Y-m-d\TH:i:s');

        return $this;
    }

    public function setEndDateTime(?\DateTime $dt): self
    {
        $this->parameters['EndDateTime'] = $dt === null ? null : $dt->format('Y-m-d\TH:i:s');

        return $this;
    }

    public function setCancelStatus(string $status): self
    {
        $this->parameters['CancelStatus'] = $status;

        return $this;
    }
}