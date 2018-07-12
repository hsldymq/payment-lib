<?php

namespace Archman\PaymentLib\Request\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Exception\SignatureException;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Validator;

/**
 * @property WeChatConfigInterface $config
 */
trait ResponseHandlerTrait
{
    /**
     * @param ResponseInterface $response
     * @return BaseResponse
     * @throws
     */
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $data = DataParser::xmlToArray($response->getBody());

        $errCode = $errMsg = null;
        if (strtoupper($data['return_code']) !== 'SUCCESS') {
            $errCode = $data['return_code'];
            $errMsg = $data['return_msg'];
        } elseif (strtoupper($data['result_code']) !== 'SUCCESS') {
            $errCode = $data['err_code'];
        }

        if ($errCode) {
            throw new ErrorResponseException($errCode, $errMsg, $data);
        }

        $signature = $data['sign'];
        $signType = $this->signType ?? $this->config->getDefaultSignType();

        // 验证响应签名
        $validator = new Validator($this->config);
        $validator->validate($signature, $signType, $data);

        return $this->getResponse($data);
    }

    protected function getResponse(array $data): BaseResponse
    {
        return new GeneralResponse($data);
    }
}