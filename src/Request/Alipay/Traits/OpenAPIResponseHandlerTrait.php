<?php
namespace Archman\PaymentLib\RequestInterface\Alipay\Traits;

use Api\Exception\Logic\VendorInterfaceResponseErrorException;
use Exception\UnavailablePropertyException;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\Request\DataParser;
use Utils\PaymentVendor\ErrorMapper\Alipay;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;

/**
 * @property AlipayConfigInterface $config
 * @property string $sign_type
 * @property string $response_data_field
 * @property string $response_sign_field
 */
trait OpenAPIResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): array
    {
        $data = DataParser::jsonToArray($response->getBody());

        // 验证错误码
        $this->checkError($data);

        $signType = $this->signType ?? $this->config->getOpenAPIDefaultSignType();
        $signature = $data[self::SIGN_FIELD];
        $data = $data[self::DATA_FIELD];

        // 验证响应签名
        $validator = new Validator($this->config);
        $validator->validateSignSync($signature, $signType, $data, true);

        return $data;
    }

    /**
     * 检查响应的错误码.
     * @param array $data
     * @return void
     * @throws VendorInterfaceResponseErrorException
     */
    private function checkError(array $data)
    {
        $data = $data[$this->response_data_field];
        if (intval($data['code']) !== 10000) {
            $error = Alipay::map($data['sub_code']);
            throw new VendorInterfaceResponseErrorException($data['sub_code'], $data, [
                'message' => "Request Alipay Interface Error, Failed Code: {$error['code']}, Failed Text: {$error['text']}"
            ]);
        }
    }
}