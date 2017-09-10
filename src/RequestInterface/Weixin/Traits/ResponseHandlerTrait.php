<?php
namespace Utils\PaymentVendor\RequestInterface\Weixin\Traits;

use Api\Exception\Logic\VendorInterfaceResponseErrorException;
use Psr\Http\Message\ResponseInterface;
use Utils\PaymentVendor\ErrorMapper\Weixin;
use Utils\PaymentVendor\SignatureHelper\Weixin\Validator;

/**
 * @property string $sign_type
 */
trait ResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): array
    {
        $data = \xml_to_array($response->getBody());

        $code = null;
        if (strtoupper($data['return_code']) !== 'SUCCESS') {
            $code = Weixin::map($data['return_msg']);
        } elseif (strtoupper($data['result_code']) !== 'SUCCESS') {
            $code = Weixin::map($data['err_code'], $data['err_code_des']);
        }

        if ($code) {
            throw new VendorInterfaceResponseErrorException(
                Weixin::lastFailedCode(),
                $data,
                ['message' => "Weixin Request Error, Failed Code: {$code['code']}, Failed Text: {$code['text']}"]
            );
        }

        $signature = $data['sign'];

        // 验证响应签名
        $validator = new Validator($this->config);
        $validator->verify($signature, $this->sign_type, $data, true);

        return $data;
    }
}