<?php
namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 提交刷卡支付.
 * @link https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10&index=1
 */
class MicroPay implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/micropay';

    private $config;

    private $signType;

    private $params = [
        'device_info' => null,
        'body' => null,
        'detail' => null,
        'attach' => null,
        'out_trade_no' => null,
        'total_fee' => null,
        'fee_type' => null,
        'spbill_create_ip' => null,
        'goods_tag' => null,
        'limit_pay' => null,
        'auth_code' => null,
        'scene_info' => null,
    ];

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

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getDefaultSignType();
    }

    public function makeParameters(bool $withSign = true): array
    {
        $detail = ParameterHelper::packValidParameters($this->detail);
        !is_null($detail) && $this->params['detail'] = json_encode($detail);

        $storeInfo = ParameterHelper::packValidParameters($this->storeInfo);
        !is_null($storeInfo) && $this->params['scene_info'] = json_encode(['store_info' => $storeInfo]);

        ParameterHelper::checkRequired($this->params, ['body', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'auth_code']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $withSign && $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setDeviceInfo(?string $info): self
    {
        $this->params['device_info'] = $info;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->params['body'] = $body;

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2
     * @param string $field
     * @param null|string $value
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
     * @param string $goodsID
     * @param string|null $wxPayGoodsID
     * @param string|null $goodsName
     * @param int $quantity
     * @param int $price
     * @return self
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

    public function setAttach(?string $attach): self
    {
        $this->params['attach'] = $attach;

        return $this;
    }

    public function setOutTradeNo(string $no): self
    {
        $this->params['out_trade_no'] = $no;

        return $this;
    }

    public function setTotalFee(int $fee): self
    {
        ParameterHelper::checkAmount($fee, 'The Total Fee Should Be Greater Than 0');
        $this->params['total_fee'] = $fee;

        return $this;
    }

    public function setFeeType(?string $type): self
    {
        $this->params['fee_type'] = $type;

        return $this;
    }

    public function setSPBillCreateIP(string $ip): self
    {
        $this->params['spbill_create_ip'] = $ip;

        return $this;
    }

    public function setGoodsTag(?string $tag): self
    {
        $this->params['goods_tag'] = $tag;

        return $this;
    }

    public function setLimitPay(?string $limit): self
    {
        $this->params['limit_pay'] = $limit;

        return $this;
    }

    public function setAuthCode(string $code): self
    {
        $this->params['auth_code'] = $code;

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
}