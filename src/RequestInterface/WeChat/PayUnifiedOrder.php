<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Utils\PaymentVendor\ConfigManager\WeixinConfig;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\ResponseHandlerTrait;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RootCATrait;
use Utils\PaymentVendor\SignatureHelper\Weixin\Generator;
use Utils\PaymentVendor\RequestInterface\Weixin\Traits\RequestPreparationTrait;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
 * @link https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2
 * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
 */
class PayUnifiedOrder implements RequestableInterface
{
    use RequestPreparationTrait;
    use ResponseHandlerTrait;
    use RootCATrait;

    private $config;

    private $uri = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private $sign_type = 'MD5';

    private $params = [
        'device_info' => null,
        'body' => null,                 // 必填
        'detail' => [
            'cost_price' => null,
            'receipt_id' => null,
            'goods_detail' => [],
        ],
        'attach' => null,
        'out_trade_no' => null,         // 必填
        'fee_type' => null,
        'total_fee' => null,            // 必填
        'spbill_create_ip' => null,     // 必填
        'time_start' => null,
        'time_expire' => null,
        'goods_tag' => null,
        'notify_url' => null,           // 必填
        'trade_type' => null,           // 必填
        'product_id' => null,
        'limit_pay' => null,
        'openid' => null,
        'scene_info' => [],
    ];

    public function __construct(WeixinConfig $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $callback = $this->params['trade_type'] === 'JSAPI' ? 'pay.wap' : 'pay.app';
        $this->setNotifyUrl($this->config->getCallbackUrl($callback));

        ParameterHelper::checkRequired($this->params, ['body', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'notify_url', 'trade_type']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = md5(microtime(true));
        $parameters['sign_type'] = $this->sign_type;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->sign_type);

        return $parameters;
    }

    public function setDeviceInfo(string $info): self
    {
        $this->params['device_info'] = $info;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
     */
    public function setBody(string $body): self
    {
        $this->params['body'] = $body;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     * @param int $amount 单位: 分
     * @return self
     */
    public function setDetailCostPrice(int $amount): self
    {
        ParameterHelper::checkAmount($amount);

        $this->params['detail']['cost_price'] = $amount;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     * @param string $receipt_id
     * @return self
     */
    public function setDetailReceiptID(string $receipt_id): self
    {
        $this->params['detail']['receipt_id'] = $receipt_id;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     * @param string $goods_id
     * @param int $quantity
     * @param int $price
     * @param string|null $wxpay_goods_id
     * @param string|null $goods_name
     * @return self
     */
    public function addDetailGoodsDetail(
        string $goods_id,
        int $quantity,
        int $price,
        string $wxpay_goods_id = null,
        string $goods_name = null
    ): self {
        $detail = [
            'goods_id' => $goods_id,
            'quantity' => $quantity,
            'price' => $price,
        ];
        $wxpay_goods_id && $detail['wxpay_goods_id'] = $wxpay_goods_id;
        $goods_name && $detail['goods_name'] = $goods_name;

        $this->params['detail']['goods_detail'][] = $detail;

        return $this;
    }

    public function setAttach(string $attach): self
    {
        $this->params['attach'] = $attach;

        return $this;
    }

    public function setOutTradeNo(string $out_trade_no): self
    {
        $this->params['out_trade_no'] = $out_trade_no;

        return $this;
    }

    public function setFeeType(string $fee_type): self
    {
        $this->params['fee_type'] = $fee_type;

        return $this;
    }

    public function setTotalFee(int $total_fee): self
    {
        $this->params['total_fee'] = $total_fee;

        return $this;
    }

    public function setSpbillCreateIP(string $ip): self
    {
        $this->params['spbill_create_ip'] = $ip;

        return $this;
    }

    public function setTimeStart(\DateTime $date_time): self
    {
        $this->params['time_start'] = $date_time->format('YmdHis');

        return $this;
    }

    public function setTimeExpire(\DateTime $date_time): self
    {
        $this->params['time_expire'] = $date_time->format('YmdHis');

        return $this;
    }

    public function setGoodsTag(string $tag): self
    {
        $this->params['good_tag'] = $tag;

        return $this;
    }

    public function setNotifyUrl(string $uri): self
    {
        $this->params['notify_url'] = $uri;

        return $this;
    }

    public function setTradeType(string $type): self
    {
        $this->params['trade_type'] = $type;

        return $this;
    }

    public function setProductID(string $id): self
    {
        $this->params['product_id'] = $id;

        return $this;
    }

    public function setLimitPay(string $limit): self
    {
        $this->params['limit_pay'] = $limit;

        return $this;
    }

    public function setOpenID(string $openid): self
    {
        $this->params['openid'] = $openid;

        return $this;
    }

    public function setSceneInfo(string $key, string $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }
}