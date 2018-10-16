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
 * 向MyCard请求授权码.
 */
class AuthGlobal implements RequestableInterface, ParameterMakerInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const TEST_URI = 'https://test.b2b.mycard520.com.tw/MyBillingPay/v1.1/AuthGlobal';

    private const PROD_URI = 'https://b2b.mycard520.com.tw/MyBillingPay/v1.1/AuthGlobal';

    private $config;

    private $parameters = [
        'FacServiceId' => null,
        'FacTradeSeq' => null,
        'TradeType' => null,
        'ServerId' => null,
        'CustomerId' => null,
        'PaymentType' => null,
        'ItemCode' => null,
        'ProductName' => null,
        'Amount' => null,
        'Currency' => null,
        'SandBoxMode' => null,
        'FacReturnURL' => null,
    ];

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
        $this->parameters['FacServiceId'] = $this->config->getFacServiceID();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->parameters, [
            'FacTradeSeq',
            'TradeType',
            'CustomerId',
            'ProductName',
            'Amount',
            'Currency',
            'SandBoxMode',
        ]);

        $parameters = ParameterHelper::packValidParameters($this->parameters);
        $parameters['Hash'] = (new Generator($this->config))->makeHash($parameters);

        return $parameters;
    }

    public function setFacTradeSeq(string $seq): self
    {
        $this->parameters['FacTradeSeq'] = $seq;

        return $this;
    }

    public function setTradeType(string $type): self
    {
        $this->parameters['TradeType'] = $type;

        return $this;
    }

    public function setServerID(?string $id): self
    {
        $this->parameters['ServerId'] = $id;

        return $this;
    }

    public function setCustomerID(string $id): self
    {
        $this->parameters['CustomerId'] = $id;

        return $this;
    }

    public function setPaymentType(?string $type): self
    {
        $this->parameters['PaymentType'] = $type;

        return $this;
    }

    public function setItemCode(?string $code): self
    {
        $this->parameters['ItemCode'] = $code;

        return $this;
    }

    public function setProductName(string $name): self
    {
        $this->parameters['ProductName'] = $name;

        return $this;
    }

    /**
     * @param int $amount 单位:分
     *
     * @return AuthGlobal
     */
    public function setAmount(int $amount): self
    {
        $amount = ParameterHelper::transAmountUnit($amount);
        $this->parameters['Amount'] = $amount;

        return $this;
    }

    /**
     * 设置货币代码.
     *
     * 货币代码要符合3-letter ISO 4217 Currency Code.
     *
     * @see https://www.iso.org/iso-4217-currency-codes.html
     *
     * @param string $currency
     *
     * @return AuthGlobal
     */
    public function setCurrency(string $currency): self
    {
        $this->parameters['Currency'] = $currency;

        return $this;
    }

    /**
     * 设置环境.
     *
     * @param bool $isSandBox true:沙盒测试环境; false:生产环境
     *
     * @return AuthGlobal
     */
    public function setSandBoxMode(bool $isSandBox): self
    {
        $this->parameters['SandBoxMode'] = $isSandBox ? 'true' : 'false';
        // 沙盒环境的数据必须向测试地址发起请求
        $this->setEnv($isSandBox);

        return $this;
    }

    /**
     * 设置回调地址.
     *
     * @param null|string $uri
     *
     * @return AuthGlobal
     */
    public function setFacReturnURL(?string $uri): self
    {
        $this->parameters['FacReturnURL'] = $uri;

        return $this;
    }
}