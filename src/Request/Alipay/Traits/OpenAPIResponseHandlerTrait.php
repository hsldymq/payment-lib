<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay\Traits;

use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\Alipay\Helper\Encryption;
use Archman\PaymentLib\Request\Alipay\Helper\OpenAPIResponseParser;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\Request\DataConverter;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\Config\AlipayConfigInterface;

/**
 * @property AlipayConfigInterface $config
 * @property string $sign_type
 */
trait OpenAPIResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $body = strval($response->getBody());
        $contentStr = OpenAPIResponseParser::getResponseContent($body, self::CONTENT_FIELD);

        $data = DataConverter::jsonToArray($body);
        $signature = $data[self::SIGN_FIELD];
        $content = $data[self::CONTENT_FIELD];

        $signType = $this->signType ?? $this->config->getOpenAPIDefaultSignType();
        (new Validator($this->config))->validateOpenAPIResponseSign($signature, $signType, $contentStr ?? $content);

        // 数据已加密
        if (is_string($content)) {
            $content = DataConverter::jsonToArray(Encryption::decrypt($content, $this->config->getOpenAPIEncryptionKey()));
            $data[self::CONTENT_FIELD] = $content;
        }

        // 验证错误码
        $this->checkError($content, $data);

        return $this->getResponse($content);
    }

    /**
     * 检查响应的错误码.
     *
     * @param array $content
     * @param array $data
     *
     * @return void
     * @throws ErrorResponseException
     */
    private function checkError(array $content, array $data)
    {
        if (intval($content['code']) !== 10000) {
            throw new ErrorResponseException($content['sub_code'], $content['sub_msg'], $data);
        }
    }

    protected function getResponse(array $content): BaseResponse
    {
        return new GeneralResponse($content);
    }
}