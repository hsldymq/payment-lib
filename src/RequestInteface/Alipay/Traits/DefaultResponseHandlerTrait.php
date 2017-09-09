<?php
namespace Utils\PaymentVendor\RequestInterface\Alipay\Traits;

use Api\Exception\Logic\VendorInterfaceResponseErrorException;
use Exception\UnavailablePropertyException;
use Psr\Http\Message\ResponseInterface;
use Utils\PaymentVendor\ConfigManager\AlipayConfig;
use Utils\PaymentVendor\DataParser;
use Utils\PaymentVendor\SignatureHelper\Alipay\Validator;

/**
 * @property AlipayConfig $config
 * @property string $sign_type
 * @property string $response_data_field
 * @property string $response_sign_field
 */
trait DefaultResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): array
    {
        // 检查handler所需的必要字段
        $this->checkProperty();

        $data = DataParser::parseJSON($response->getBody());
        print_r($data);
        // 验证错误码
        $this->checkError($data);

        $signature = $data[$this->response_sign_field];
        $data = $data[$this->response_data_field];

        // 验证响应签名
        $validator = new Validator($this->config);
        $validator->verifySync($signature, $this->sign_type, $data, true);

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
            throw new VendorInterfaceResponseErrorException($data['sub_code'], $data);
        }
    }

    private function checkProperty()
    {
        if (!($this->response_data_field ?? false)) {
            throw new UnavailablePropertyException('Unavailable Property For Alipay Interface Response Handler: response_data_field');
        }
        if (!($this->response_sign_field ?? false)) {
            throw new UnavailablePropertyException('Unavailable Property For Alipay Interface Response Handler: response_sign_field');
        }
        if (!($this->sign_type ?? false)) {
            throw new UnavailablePropertyException('Unavailable Property For Alipay Interface Response Handler: sign_type');
        }
        if (!($this->config ?? false)) {
            throw new UnavailablePropertyException('Unavailable Property For Alipay Interface Response Handler: config');
        }
        if (!($this->config instanceof AlipayConfig)) {
            throw new UnavailablePropertyException('Unavailable Property For Alipay Interface Response Handler: config Should Be An Instance Of AlipayConfig');
        }
    }
}