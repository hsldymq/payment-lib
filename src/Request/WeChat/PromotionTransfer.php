<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Config\WeChatConfigInterface;
use Archman\PaymentLib\Request\Alipay\Traits\DefaultSenderTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

class PromotionTransfer implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use DefaultSenderTrait;

    private WeChatConfigInterface $config;

    private string $sign_type = 'MD5';

    private string $uri = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    private array $params = [
        'partner_trade_no' => null,
        'openid' => null,
        'check_name' => null,
        're_user_name' => null,
        'amount' => null,
        'desc' => null,
        'spbill_create_ip' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['partner_trade_no', 'openid', 'check_name', 'amount', 'desc']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['mch_appid'] = $this->config->getAppID();
        $parameters['mchid'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = md5(strval(microtime(true)));
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    public function setPartnerTradeNo(?string $no): self
    {
        $this->params['partner_trade_no'] = $no;

        return $this;
    }

    public function setOpenID(?string $id): self
    {
        $this->params['openid'] = $id;

        return $this;
    }

    public function setCheckName(?string $v): self
    {
        $this->params['check_name'] = $v;

        return $this;
    }

    public function setReUserName(?string $name): self
    {
        $this->params['re_user_name'] = $name;

        return $this;
    }

    /**
     * @param int|null $amount 单位: 分
     *
     * @return self
     */
    public function setAmount(?int $amount): self
    {
        $this->params['amount'] = $amount;

        return $this;
    }

    public function setDesc(?string $desc): self
    {
        $this->params['desc'] = $desc;

        return $this;
    }

    public function setSPBillCreateIP(?string $ip): self
    {
        $this->params['spbill_create_ip'] = $ip;

        return $this;
    }
}