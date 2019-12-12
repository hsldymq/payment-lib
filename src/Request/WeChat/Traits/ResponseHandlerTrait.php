<?php

namespace Archman\PaymentLib\Request\WeChat\Traits;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\DataParser;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Validator;

/**
 * @property WeChatConfigInterface $config
 * @property string $fixedSignType
 */
trait ResponseHandlerTrait
{
    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $data = $this->parseXMLDataAndCheck($response->getBody()->getContents());

        // 验证响应签名
        $validator = new Validator($this->config);
        $signType = $this->fixedSignType ?? $this->config->getSignType();
        $validator->validate($data['sign'], $signType, $data);

        return $data;
    }

    private function parseXMLDataAndCheck(string $body): array
    {
        $data = DataParser::xmlToArray($body);

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

        return $data;
    }
}