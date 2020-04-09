<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 申请退款接口.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_4&index=6
 */
class Refund implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    private $config;

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
        'out_refund_no' => null,
        'total_fee' => null,
        'refund_fee' => null,
        'refund_fee_type' => null,
        'refund_desc' => null,
        'refund_account' => null,
        'notify_url' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['out_refund_no', 'total_fee', 'refund_fee'], ['transaction_id', 'out_trade_no']);

        $signType = $this->config->getSignType();
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType);

        return $parameters;
    }

    public function setTransactionID(string $id): self
    {
        $this->params['transaction_id'] = $id;

        return $this;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function setOutRefundNo(string $no): self
    {
        $this->params['out_refund_no'] = $no;

        return $this;
    }

    public function setTotalFee(int $fee): self
    {
        ParameterHelper::checkAmount($fee, "The Total Fee Should Be Greater Than 0");
        $this->params['total_fee'] = $fee;

        return $this;
    }

    public function setRefundFee(int $fee): self
    {
        ParameterHelper::checkAmount($fee, "The Refund Fee Should Be Greater Than 0");
        $this->params['refund_fee'] = $fee;

        return $this;
    }

    public function setRefundFeeType(?string $type): self
    {
        $this->params['refund_fee_type'] = $type;

        return $this;
    }

    public function setRefundDescription(?string $desc): self
    {
        $this->params['refund_desc'] = $desc;

        return $this;
    }

    public function setRefundAccount(?string $account): self
    {
        $this->params['refund_account'] = $account;

        return $this;
    }

    public function setNotifyURL(?string $uri): self
    {
        $this->params['notify_url'] = $uri;

        return $this;
    }

    public function send(?BaseClient $client = null): BaseResponse
    {
        $client = $client ?? new Client();
        $response = $client->sendRequest($this);

        return $this->handleResponse($response);
    }

    protected function customRequestOption(RequestOption $option): RequestOption
    {
        $option->setRootCAFilePath($this->config->getRootCAPath())
            ->setSSLKeyFilePath($this->config->getSSLKeyPath())
            ->setSSLKeyPassword($this->config->getSSLKeyPassword())
            ->setSSLCertFilePath($this->config->getClientCertPath())
            ->setSSLCertPassword($this->config->getClientCertPassword());

        return $option;
    }
}