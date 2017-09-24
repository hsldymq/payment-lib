<?php
namespace Utils\PaymentVendor\RequestInterface\Weixin;

use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RequestPreparationTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\ResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RootCATrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;

class QueryOrder implements RequestableInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use RootCATrait;

    private $config;

    private $sign_type = 'MD5';

    private $uri = 'https://api.mch.weixin.qq.com/pay/orderquery';

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
    ];

    public function __construct(WeixinConfig $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, [], ['transaction_id', 'out_trade_no']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = md5(microtime(true));
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    public function setTransactionID(string $transaction_id): self
    {
        $this->params['transaction_id'] = $transaction_id;

        return $this;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->params['out_trade_no'] = $out_trade_no;

        return $this;
    }
}