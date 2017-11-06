<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\InvalidParameterException;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 下载对账单.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_6
 */
class DownloadBill implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    private $config;

    private $signType;

    private $params = [
        'device_info' => null,
        'bill_date' => null,
        'bill_type' => null,
        'tar_type' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getDefaultSignType();
    }

    public function makeParameters(bool $withSign = true): array
    {
        ParameterHelper::checkRequired($this->params, ['bill_date', 'bill_type']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $withSign && $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setBeginTime(\DateTime $datetime): self
    {
        $this->params['begin_time'] = $datetime->format('YmdHis');

        return $this;
    }

    public function setEndTime(\DateTime $datetime): self
    {
        $this->params['end_time'] = $datetime->format('YmdHis');

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    public function setDeviceInfo(?string $info): self
    {
        $this->params['device_info'] = $info;

        return $this;
    }

    public function setBillDate(\DateTime $dt): self
    {
        $this->params['bill_date'] = $dt->format('Ymd');

        return $this;
    }

    public function setBillType(string $type): self
    {
        if (!in_array($type, ['ALL', 'SUCCESS', 'REFUND', 'RECHARGE_REFUND'])) {
            throw new InvalidParameterException('bill_type', "Invalid Value For Bill Type({$type}), Should Be One Of These(ALL/SUCCESS/REFUND/RECHARGE_REFUND).");
        }

        $this->params['bill_type'] = $type;

        return $this;
    }

    public function setTarType(string $type): self
    {
        if ($type !== 'GZIP') {
            throw new InvalidParameterException('tar_type', "The Value Of Tar Type Should Be 'GZIP' Only.");
        }

        $this->params['tar_type'] = $type;

        return $this;
    }
}