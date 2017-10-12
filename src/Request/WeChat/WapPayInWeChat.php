<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\RequestInterface\Client;
use Archman\PaymentLib\RequestInterface\Helper\ParameterHelper;
use Archman\PaymentLib\RequestInterface\MutableDateTimeInterface;
use Archman\PaymentLib\RequestInterface\Traits\MutableDateTimeTrait;
use Archman\PaymentLib\SignatureHelper\Weixin\Generator;

/**
 * 微信内H5调起支付.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6
 */
class WapPayInWeChat implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

    private $config;

    private $sign_type = 'MD5';

    /** @var UnifiedOrder */
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

    public function setUnifiedOrder(UnifiedOrder $order): self
    {
        $this->unified_order = $order;

        return $this;
    }
}