<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Config\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 交易保障.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_8
 */
class Report implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private const URI = 'https://api.mch.weixin.qq.com/payitil/report';

    private WeChatConfigInterface $config;

    private string $signType;

    private array $params = [
        'device_info' => null,
        'interface_url' => null,
        'execute_time' => null,
        'return_code' => null,
        'return_msg' => null,
        'result_code' => null,
        'err_code' => null,
        'err_code_des' => null,
        'out_trade_no' => null,
        'user_ip' => null,
        'time' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getSignType();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['interface_url', 'execute_time', 'return_code', 'result_code', 'user_ip']);

        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setDeviceInfo(?string $info): self
    {
        $this->params['device_info'] = $info;

        return $this;
    }

    public function setInterfaceURL(string $url): self
    {
        $this->params['interface_url'] = $url;

        return $this;
    }

    public function setExecuteTime(int $time): self
    {
        $this->params['execute_time'] = $time;

        return $this;
    }

    public function setReturnCode(string $code): self
    {
        $this->params['return_code'] = $code;

        return $this;
    }

    public function setReturnMsg(?string $msg): self
    {
        $this->params['return_msg'] = $msg;

        return $this;
    }

    public function setResultCode(string $code): self
    {
        $this->params['result_code'] = $code;

        return $this;
    }

    public function setErrCode(?string $code): self
    {
        $this->params['err_code'] = $code;

        return $this;
    }

    public function setErrCodeDes(?string $des): self
    {
        $this->params['err_code_des'] = $des;

        return $this;
    }

    public function setOutTradeNo(?string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function setUserIP(string $ip): self
    {
        $this->params['user_ip'] = $ip;

        return $this;
    }

    public function setTime(?\DateTime $dt): self
    {
        $this->params['time'] = $dt ? $dt->format('YmdHis') : null;

        return $this;
    }
}