<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\RequestInterface\WeChat\PayUnifiedOrder;
use Utils\PaymentVendor\RequestInterface\Client;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * TODO 有待验证
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_12&index=2
 */
class AppPay implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

    private $config;

    /** @var PayUnifiedOrder */
    private $unifiedOrder = null;

    private $params = [
        'prepayid' => null,
        'package' => 'Sign=WXPay',
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        if (!$this->params['prepayid'] && $this->unifiedOrder) {
            $data = Client::sendRequest($this->unifiedOrder);
            $this->setPrepayID($data['prepay_id']);
        }

        ParameterHelper::checkRequired($this->params, ['prepayid', 'package']);

        $now = $this->getDateTime();
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['partnerid'] = $this->config->getMerchantID();
        $parameters['noncestr'] = md5(microtime(true));
        $parameters['timestamp'] = $now->getTimestamp();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters);

        return $parameters;
    }

    public function setPrepayID(string $prepayID): self
    {
        $this->params['prepayid'] = $prepayID;

        return $this;
    }

    public function setUnifiedOrder(PayUnifiedOrder $order): self
    {
        $this->unifiedOrder = $order;

        return $this;
    }
}