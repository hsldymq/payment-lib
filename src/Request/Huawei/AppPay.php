<?php

namespace Archman\PaymentLib\Request\Huawei;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\SignatureHelper\Huawei\Generator;

/**
 * 应用内支付生成参数.
 *
 * @see https://developer.huawei.com/consumer/cn/service/hms/catalog/fastapp.html?page=fastapp_fastapp_api_reference_pay
 */
class AppPay implements ParameterMakerInterface
{
    private $config;

    private $params = [
        'productName' => null,
        'productDesc' => null,
        'requestId' => null,
        'amount' => null,
        'serviceCatalog' => null,
        'merchantName' => null,
        'sdkChannel' => null,
        'url' => null,
        'currency' => null,
        'country' => null,
        'urlVer' => null,
        'extReserved' => null,
        'expireTime' => null,
        'partnerIDs' => null,
        'validTime' => null,
        'publicKey' => null,
    ];

    public function __construct(HuaweiConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['productName', 'productDesc', 'requestId', 'amount', 'serviceCatalog', 'merchantName']);
        $generator = new Generator($this->config);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['applicationID'] = $this->config->getAppID();
        $parameters['merchantId'] = $this->config->getMerchantID();
        // 签名时urlVer需要变为urlver
        $p = $parameters;
        isset($p['urlVer']) && $p['urlver'] = $p['urlVer'];
        unset($p['urlVer']);
        $parameters['sign'] = $generator->makeSign($p, ['serviceCatalog', 'merchantName', 'extReserved', 'inSign', 'publicKey']);

        return $parameters;
    }

    public function setProductName(string $name): self
    {
        $this->params['productName'] = $name;

        return $this;
    }

    public function setProductDesc(string $desc): self
    {
        $this->params['productDesc'] = $desc;

        return $this;
    }

    public function setRequestID(string $id): self
    {
        $this->params['requestId'] = $id;

        return $this;
    }

    /**
     * @param int $amount 单位:分
     *
     * @return self
     */
    public function setAmount(int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->params['amount'] = ParameterHelper::transAmountUnit($amount);

        return $this;
    }

    public function setServiceCatalog(string $catalog): self
    {
        $this->params['serviceCatalog'] = $catalog;

        return $this;
    }

    public function setMerchantName(string $name): self
    {
        $this->params['merchantName'] = $name;

        return $this;
    }

    public function setSDKChannel(int $channel): self
    {
        $this->params['sdkChannel'] = $channel;

        return $this;
    }

    /**
     * 设置回调地址.
     *
     * @param null|string $url
     *
     * @return AppPay
     */
    public function setURL(?string $url): self
    {
        $this->params['url'] = $url;

        return $this;
    }

    public function setCurrency(?string $currency): self
    {
        $this->params['currency'] = $currency;

        return $this;
    }

    public function setCountry(?string $country): self
    {
        $this->params['country'] = $country;

        return $this;
    }

    public function setURLVer(?string $ver): self
    {
        $this->params['urlVer'] = $ver;

        return $this;
    }

    public function setExtReserved(?string $reserved): self
    {
        $this->params['extReserved'] = $reserved;

        return $this;
    }

    public function setPublicKey(string $key): self
    {
        $this->params['publicKey'] = $key;

        return $this;
    }

    /**
     * TODO 官方暂不支持
     *
     * @param \DateTime|null $datetime
     *
     * @return AppPay
     */
    public function setExpireTime(?\DateTime $datetime): self
    {
        // $this->params['expireTime'] = $datetime->getTimestamp();

        return $this;
    }

    /**
     * TODO 官方暂不支持
     *
     * @param array|null $ids
     *
     * @return AppPay
     */
    public function setPartnerIDs(?array $ids): self
    {
        // $this->params['partnerIDs'] = json_encode($ids);

        return $this;
    }

    /**
     * TODO 官方暂不支持
     *
     * @param int|null $seconds
     *
     * @return AppPay
     */
    public function setValidTime(?int $seconds): self
    {
        // $this->params['validTime'] = $seconds;

        return $this;
    }
}