<?php
namespace Utils\PaymentVendor\RequestInterface\Weixin;

use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Client;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\MutableDateTimeInterface;
use Utils\PaymentVendor\RequestInterface\Traits\MutableDateTimeTrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;

/**
 * TODO 有待验证
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_12&index=2
 */
class AppPay implements MutableDateTimeInterface
{
    use MutableDateTimeTrait;

    private $config;

    private $sign_type = 'MD5';

    /** @var UnifiedOrder */
    private $unified_order = null;

    private $params = [
        'prepayid' => null,
        'package' => 'Sign=WXPay',
    ];

    public function __construct(WeixinConfig $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        if (!$this->params['prepayid'] && $this->unified_order) {
            $data = Client::sendRequest($this->unified_order);
            $this->setPrepayID($data['prepay_id']);
        }

        ParameterHelper::checkRequired($this->params, ['prepayid', 'package']);

        $now = $this->getDateTime();
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['partnerid'] = $this->config->getMerchantID();
        $parameters['noncestr'] = md5(microtime(true));
        $parameters['timestamp'] = $now->getTimestamp();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    public function setPrepayID(string $prepay_id): self
    {
        $this->params['prepayid'] = $prepay_id;

        return $this;
    }

    public function setUnifiedOrder(UnifiedOrder $order): self
    {
        $this->unified_order = $order;

        return $this;
    }
}