<?php
namespace Archman\PaymentLib\Request\Huawei;

use Archman\PaymentLib\ConfigManager\HuaweiConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\SignatureHelper\Huawei\Generator;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

/**
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

    public function makeParameters(bool $withSign = true): array
    {
        ParameterHelper::checkRequired($this->params, ['productName', 'productDesc', 'requestId', 'amount', 'serviceCatalog', 'merchantName']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['applicationID'] = $this->config->getAppID();
        $parameters['merchantId'] = $this->config->getMerchantID();
        // TODO 生成inSign
        $this->params['ingftAmt'] && $this->makeInSign($this->params['ingftAmt'], $this->params['requestId'], ''/* TODO */);

        $withSign && $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, ['serviceCatalog', 'merchantName', 'extReserved', 'ingftAmt', 'inSign',]);

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
     * @param int $amount 单位: 分
     * @return AppPay
     */
    public function setIngftAmt(?int $amount): self
    {
        ParameterHelper::checkAmount($amount);
        $this->params['ingftAmt'] = $amount;

        return $this;
    }

    public function setExpireTime(?\DateTime $datetime): self
    {
        $this->params['expireTime'] = $datetime->getTimestamp();

        return $this;
    }

    public function setPartnerIDs(?array $ids): self
    {
        $this->params['partnerIDs'] = json_encode($ids);

        return $this;
    }

    public function setValidTime(?int $seconds): self
    {
        $this->params['validTime'] = $seconds;

        return $this;
    }

    private function makeInSign(string $ingftAmt, string $requestID, string $developUserSign): string
    {

    }
}