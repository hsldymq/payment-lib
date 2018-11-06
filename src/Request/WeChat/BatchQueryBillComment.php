<?php

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Exception\InvalidParameterException;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\WeChat\Traits\EnvironmentTrait;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\WeChat\BillCommentResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;
use Psr\Http\Message\ResponseInterface;

/**
 * 拉取订单评价数据.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_17&index=11
 */
class BatchQueryBillComment implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use EnvironmentTrait;
    use RequestPreparationTrait;

    private const FIXED_SIGN_TYPE = 'HMAC-SHA256';

    private const URI = 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment';

    private $config;

    private $params = [
        'begin_time' => null,
        'end_time' => null,
        'offset' => null,
        'limit' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['begin_time', 'end_time', 'offset']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        // 不填写签名类型,并且签名时把limit排除,否则微信会返回签名错误
        // $parameters['sign_type'] = self::FIXED_SIGN_TYPE;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, self::FIXED_SIGN_TYPE, ['limit']);

        return $parameters;
    }

    public function setBeginTime(\DateTime $dt): self
    {
        $this->params['begin_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setEndTime(\DateTime $dt): self
    {
        $this->params['end_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    public function setLimit(?int $limit): self
    {
        if ($limit !== null && ($limit > 200 || $limit < 1)) {
            throw new InvalidParameterException("Invalid Limit Number({$limit}).");
        }

        $this->params['limit'] = $limit;

        return $this;
    }

    protected function customRequestOption(RequestOption $option): RequestOption
    {
        $option->setRootCAFilePath($this->config->getRootCAPath())
            ->setSSLKeyFilePath($this->config->getSSLKeyPath())
            ->setSSLKeyPassword($this->config->getSSLKeyPassword())
            ->setSSLCertFilePath($this->config->getClientCertPath())
            ->setSSLCertPassword($this->config->getClientCertPassword());

        return $option;
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

        $errCode = $errMsg = null;
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

        return new BillCommentResponse($rawBody);
    }
}