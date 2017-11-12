<?php
namespace Archman\PaymentLib\Request\WeChat;
use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 撤销订单.
 * @link https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_11&index=3
 */
class Reverse implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';

    private $config;

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, [], ['transaction_id', 'out_trade_no']);

        $signType = $this->config->getDefaultSignType();
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $signType);

        return $parameters;
    }

    public function setTransactionID(?string $id): self
    {
        $this->params['transaction_id'] = $id;

        return $this;
    }

    public function setOutTradeNo(?string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
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