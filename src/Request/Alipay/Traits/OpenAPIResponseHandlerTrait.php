<?php
namespace Archman\PaymentLib\Request\Alipay\Traits;

use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Exception\SignatureException;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

/**
 * @property AlipayConfigInterface $config
 * @property string $sign_type
 */
trait OpenAPIResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $data = DataParser::jsonToArray($response->getBody());

        // 验证错误码
        $this->checkError($data);

        $signType = $this->signType ?? $this->config->getOpenAPIDefaultSignType();
        $signature = $data[self::SIGN_FIELD];
        $content = $data[self::CONTENT_FIELD];

        // 验证响应签名
        $validator = new Validator($this->config);
        try {
            $validator->validateSignSync($signature, $signType, $content);
        } catch (\Throwable $e) {
            if (!($e instanceof SignatureException)) {
                $e = new SignatureException($data, $e->getMessage(), 0, $e);
            }
            throw $e;
        }

        return $this->getResponse($content);
    }

    /**
     * 检查响应的错误码.
     * @param array $data
     * @return void
     * @throws ErrorResponseException
     */
    private function checkError(array $data)
    {
        $content = $data[self::CONTENT_FIELD];
        if (intval($content['code']) !== 10000) {
            throw new ErrorResponseException(
                $content['sub_code'],
                $content['sub_msg'],
                $data
            );
        }
    }

    protected function getResponse(array $content): BaseResponse
    {
        return new GeneralResponse($content);
    }
}