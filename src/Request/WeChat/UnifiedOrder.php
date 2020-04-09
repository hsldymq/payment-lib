<?php

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
 * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
 * @see https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2
 * @see https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
 */
class UnifiedOrder implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private $config;

    private $signType;

    private $detail = [
        'cost_price' => null,
        'receipt_id' => null,
        'goods_detail' => [],
    ];

    private $storeInfo = [
        'id' => null,
        'name' => null,
        'area_code' => null,
        'address' => null,
    ];

    private $h5Info = [
        'type' => null,
        'app_name' => null,
        'bundle_id' => null,
        'package_name' => null,
        'wap_name' => null,
        'wap_url' => null,
    ];

    private $params = [
        'device_info' => null,
        'body' => null,                 // 必填
        'detail' => null,
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
        'scene_info' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getSignType();
    }

    public function makeParameters(): array
    {
        $detail = ParameterHelper::packValidParameters($this->detail);
        !is_null($detail) && $this->params['detail'] = json_encode($detail);

        $info = [];
        $storeInfo = ParameterHelper::packValidParameters($this->storeInfo);
        !is_null($storeInfo) && $info['store_info'] = $storeInfo;
        $h5Info = ParameterHelper::packValidParameters($this->h5Info);
        !is_null($h5Info) && $info['h5_info'] = $h5Info;
        $info && $this->params['scene_info'] = json_encode($info);

        ParameterHelper::checkRequired($this->params, ['body', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'notify_url', 'trade_type']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setDeviceInfo(string $info): self
    {
        $this->params['device_info'] = $info;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_2
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->params['body'] = $body;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     *
     * @param string $field
     * @param null|string $value
     *
     * @return $this
     */
    public function setDetail(string $field, ?string $value): self
    {
        if ($field === 'goods_detail') {
            return $this;
        }

        $this->detail[$field] = $value;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     *
     * @param string $goodsID
     * @param string|null $wxPayGoodsID
     * @param string|null $goodsName
     * @param int $quantity
     * @param int $price
     *
     * @return self
     * @throws
     */
    public function addGoodsDetail(string $goodsID, ?string $wxPayGoodsID, ?string $goodsName, int $quantity, int $price): self
    {
        ParameterHelper::checkAmount($quantity, 'The Quantity Should Be Greater Than 0');
        ParameterHelper::checkAmount($price, 'The Price Should Be Greater Than 0');
        $detail = [
            'goods_id' => $goodsID,
            'quantity' => $quantity,
            'price' => $price,
        ];
        $wxPayGoodsID && $detail['wxpay_goods_id'] = $wxPayGoodsID;
        $goodsName && $detail['goods_name'] = $goodsName;

        $this->detail['goods_detail'][] = $detail;

        return $this;
    }

    public function setAttach(string $attach): self
    {
        $this->params['attach'] = $attach;

        return $this;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function setFeeType(string $type): self
    {
        $this->params['fee_type'] = $type;

        return $this;
    }

    public function setTotalFee(int $fee): self
    {
        ParameterHelper::checkAmount($fee, "The Total Fee Should Be Greater Than 0");
        $this->params['total_fee'] = $fee;

        return $this;
    }

    public function setSPBillCreateIP(string $ip): self
    {
        $this->params['spbill_create_ip'] = $ip;

        return $this;
    }

    public function setTimeStart(\DateTime $dt): self
    {
        $this->params['time_start'] = $dt->format('YmdHis');

        return $this;
    }

    public function setTimeExpire(\DateTime $dt): self
    {
        $this->params['time_expire'] = $dt->format('YmdHis');

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

    /**
     * trade_type=NATIVE需要设置这个.
     *
     * @param string $id
     *
     * @return self
     */
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

    /**
     * trade_type=JSAPI时需要设置openid.
     *
     * @param string $openid
     *
     * @return self
     */
    public function setOpenID(?string $openid): self
    {
        $this->params['openid'] = $openid;

        return $this;
    }

    public function setStoreInfo(?string $id = null, ?string $name = null, ?string $areaCode = null, ?string $address = null): self
    {
        $id && $this->storeInfo['id'] = $id;
        $name && $this->storeInfo['name'] = $name;
        $areaCode && $this->storeInfo['area_code'] = $areaCode;
        $address && $this->storeInfo['address'] = $address;

        return $this;
    }

    public function setSceneInfo(?array $info): self
    {
        $this->params['scene_info'] = $info;

        return $this;
    }

    public function setH5InfoIOS(string $appName, string $bundleID):self
    {
        $this->clearH5Info();
        $this->h5Info['type'] = 'IOS';
        $this->h5Info['app_name'] = $appName;
        $this->h5Info['bundle_id'] = $bundleID;

        return $this;
    }

    public function setH5InfoAndroid(string $appName, string $packageName): self
    {
        $this->clearH5Info();
        $this->h5Info['type'] = 'Android';
        $this->h5Info['app_name'] = $appName;
        $this->h5Info['package_name'] = $packageName;

        return $this;
    }

    public function setH5InfoWap(string $wapName, string $wapURL): self
    {
        $this->clearH5Info();
        $this->h5Info['type'] = 'Wap';
        $this->h5Info['wap_name'] = $wapName;
        $this->h5Info['wap_url'] = $wapURL;

        return $this;
    }

    public function send(?BaseClient $client = null): BaseResponse
    {
        $response = $client ? $client->sendRequest($this) : Client::send($this);

        return $this->handleResponse($response);
    }

    private function clearH5Info()
    {
        foreach ($this->h5Info as $key => $value) {
            $this->h5Info[$key] = null;
        }
    }
}