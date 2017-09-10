<?php
namespace Utils\PaymentVendor\RequestInterface\Apple;

use Api\Exception\Logic\MakePaymentVendorParametersFailedException;
use Api\Exception\Logic\VendorInterfaceResponseErrorException;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Utils\PaymentVendor\DataParser;
use Utils\PaymentVendor\ErrorMapper\Apple;
use Utils\PaymentVendor\RequestInterface\Helper\ParameterHelper;
use Utils\PaymentVendor\RequestInterface\RequestableInterface;

/**
 * @link https://developer.apple.com/library/content/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html#//apple_ref/doc/uid/TP40010573-CH104-SW1 文档地址
 */
class IAPReceiptValidation implements RequestableInterface
{
    private static $sandbox_uri = 'https://sandbox.itunes.apple.com/verifyReceipt';

    private static $production_uri = 'https://buy.itunes.apple.com/verifyReceipt';

    private $params = [
        'receipt-data' => null,     // 必填
        'password' => null,
        'exclude-old-transactions' => null,
    ];

    private $uri = null;

    public function handleResponse(ResponseInterface $response): array
    {
        $data = DataParser::parseJSON(strval($response->getBody()));

        $status = strval($data['status']);
        if ($status !== '0') {
            $error = Apple::map($status);
            throw new VendorInterfaceResponseErrorException($status, $data, [
                'message' => "Apple Receipt Error, Failed Code: {$error['code']}, Failed Text: {$error['text']}"
            ]);
        }

        return $data;
    }

    public function prepareRequest(): RequestInterface
    {
        ParameterHelper::checkRequired($this->params, ['receipt-data']);

        $headers = ['content-type' => 'application/x-www-form-urlencoded'];
        $params = ParameterHelper::packValidParameters($this->params);
        $body = stream_for(json_encode($params));

        return new Request('POST', $this->getUri(), $headers, $body);
    }

    public function setEnvironment(bool $is_production): self
    {
        $this->uri = $is_production ? self::$production_uri : self::$sandbox_uri;

        return $this;
    }

    public function setReceiptData(string $receipt): self
    {
        $this->params['receipt-data'] = $receipt;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->params['password'] = $password;

        return $this;
    }

    public function setExcludeOldTransactions(bool $exclude)
    {
        $this->params['exclude-old-transactions'] = $exclude;

        return $this;
    }

    public function prepareCert(&$root_ca_file, &$ssl_key_path, &$ssl_password, &$client_cert_path, &$client_cert_password)
    {
        $root_ca_file = false;
    }

    private function getUri(): string
    {
        if (!$this->uri) {
            throw new MakePaymentVendorParametersFailedException(['message' => "Didn't Set Environment"]);
        }

        return $this->uri;
    }
}