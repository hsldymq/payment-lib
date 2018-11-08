<?php

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Exception\InvalidParameterException;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\WeChat\BillResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;
use Psr\Http\Message\ResponseInterface;

/**
 * 下载对账单.
 *
 * FBI Warning: 这个接口微信给过来的数据开头带BOM表示,并且按照\r\n分割,文档里没有说明,牛皮不?
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_6
 */
class DownloadBill implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    private $config;

    private $signType;

    private $params = [
        'device_info' => null,
        'bill_date' => null,
        'bill_type' => null,
        'tar_type' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getDefaultSignType();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['bill_date', 'bill_type']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);
        //print_r($parameters);exit();
        return $parameters;
    }

    public function setBillDate(\DateTime $dt): self
    {
        $this->params['bill_date'] = $dt->format('Ymd');

        return $this;
    }

    public function setBillType(string $type): self
    {
        if (!in_array($type, ['ALL', 'SUCCESS', 'REFUND', 'RECHARGE_REFUND'])) {
            throw new InvalidParameterException('bill_type', "Invalid Value For Bill Type({$type}), Should Be One Of These(ALL/SUCCESS/REFUND/RECHARGE_REFUND).");
        }

        $this->params['bill_type'] = $type;

        return $this;
    }

    public function setTarType(?string $type): self
    {
        if ($type !== null && $type !== 'GZIP') {
            throw new InvalidParameterException('tar_type', "The Value Of Tar Type Should Be 'GZIP' Only.");
        }

        $this->params['tar_type'] = $type;

        return $this;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return BaseResponse
     * @throws
     */
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $rawBody = strval($response->getBody());

        $errCode = $errMsg = $data = null;
        if (strpos($rawBody, '<xml>') === 0) {
            $data = DataParser::xmlToArray($rawBody);
            if (strtoupper($data['return_code']) !== 'SUCCESS') {
                $errCode = $data['return_code'];
                $errMsg = $data['return_msg'];
            } elseif (strtoupper($data['result_code']) !== 'SUCCESS') {
                $errCode = $data['err_code'];
            }
        }
        if ($errCode) {
            throw new ErrorResponseException($errCode, $errMsg, $data);
        }

        if ($this->params['tar_type'] === 'GZIP') {
            $rawBody = gzdecode($rawBody);
        }

        return new BillResponse($rawBody);
    }
}