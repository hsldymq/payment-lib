<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 申请退款接口.
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_4&index=6
 */
class Refund implements RequestableInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    private $config;

    private $signType;

    private $params = [
        'transaction_id' => null,
        'out_trade_no' => null,
        'out_refund_no' => null,
        'total_fee' => null,
        'refund_fee' => null,
        'refund_fee_type' => null,
        'refund_desc' => null,
        'refund_account' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getDefaultSignType();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['out_refund_no', 'total_fee', 'refund_fee'], ['transaction_id', 'out_trade_no']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

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

    public function setRefundFeeType(string $type): self
    {
        $this->params['refund_fee_type'] = $type;

        return $this;
    }

    public function setRefundDescription(string $desc): self
    {
        $this->params['refund_desc'] = $desc;

        return $this;
    }

    public function setRefundAccount(string $account): self
    {
        $this->params['refund_account'] = $account;

        return $this;
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        $option = (new RequestOption())->setRootCAFilePath($this->config->getRootCAPath())
            ->setSSLKeyFilePath($this->config->getSSLKeyPath())
            ->setSSLPassword($this->config->getSSLKeyPassword())
            ->setClientCertFilePath($this->config->getClientCertPath())
            ->setClientCertPassword($this->config->getClientCertPassword());

        return $option;
    }
}