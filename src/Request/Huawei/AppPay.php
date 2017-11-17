<?php
namespace Archman\PaymentLib\Request\Huawei;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\SignatureHelper\Huawei\Generator;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

/**
 * 应用内支付生成参数.
 * @link http://developer.huawei.com/consumer/cn/wiki/index.php?title=HMS%E5%BC%80%E5%8F%91%E6%8C%87%E5%AF%BC%E4%B9%A6-%E5%BA%94%E7%94%A8%E5%86%85%E6%94%AF%E4%BB%98%E6%8E%A5%E5%8F%A3&oldid=4858
 * @link http://developer.huawei.com/consumer/cn/service/hms/catalog/huaweiiap.html?page=hmssdk_huaweiiap_api_reference_c1
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
        'urlver' => null,
        'extReserved' => null,
        'ingftAmt' => null,
        'expireTime' => null,
        'partnerIDs' => null,
        'validTime' => null,
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
        // TODO 生成inSign, 目前官方暂不支持
        //$this->params['ingftAmt'] && $generator->makeInSign($this->params['ingftAmt'], $this->params['requestId'], ''/* TODO */);
        $parameters['sign'] = $generator->makeSign($parameters, ['serviceCatalog', 'merchantName', 'extReserved', 'ingftAmt', 'inSign']);

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
     * @param null|string $url
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
        $this->params['urlver'] = $ver;

        return $this;
    }

    public function setExtReserved(?string $reserved): self
    {
        $this->params['extReserved'] = $reserved;

        return $this;
    }

    /**
     * TODO 官方暂不支持
     * @param int $amount 单位: 分
     * @return AppPay
     */
    public function setIngftAmt(?int $amount): self
    {
        // ParameterHelper::checkAmount($amount);
        // $this->params['ingftAmt'] = $amount;

        return $this;
    }

    /**
     * TODO 官方暂不支持
     * @param \DateTime|null $datetime
     * @return AppPay
     */
    public function setExpireTime(?\DateTime $datetime): self
    {
        // $this->params['expireTime'] = $datetime->getTimestamp();

        return $this;
    }

    /**
     * TODO 官方暂不支持
     * @param array|null $ids
     * @return AppPay
     */
    public function setPartnerIDs(?array $ids): self
    {
        // $this->params['partnerIDs'] = json_encode($ids);

        return $this;
    }

    /**
     * TODO 官方暂不支持
     * @param int|null $seconds
     * @return AppPay
     */
    public function setValidTime(?int $seconds): self
    {
        // $this->params['validTime'] = $seconds;

        return $this;
    }
}