<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Client;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;

/**
 * TODO 有待验证
 * 微信内H5调起支付.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6
 */
class WapPayInWeixin implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

    private $config;

    private $sign_type = 'MD5';

    /** @var PayUnifiedOrder */
    private $unified_order = null;

    private $params = [
        'package' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        if (!$this->params['package'] && $this->unified_order) {
            $data = Client::sendRequest($this->unified_order);
            $this->setPrepayID($data['prepay_id']);
        }

        ParameterHelper::checkRequired($this->params, ['package']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appId'] = $this->config->getAppID();
        $parameters['timeStamp'] = $this->getDateTime()->getTimestamp();
        $parameters['nonceStr'] = md5(microtime(true));
        $parameters['signType'] = $this->sign_type;
        $parameters['paySign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    public function setPrepayID(string $prepay_id): self
    {
        $this->params['package'] = "prepay_id={$prepay_id}";

        return $this;
    }

    public function setUnifiedOrder(PayUnifiedOrder $order): self
    {
        $this->unified_order = $order;

        return $this;
    }
}