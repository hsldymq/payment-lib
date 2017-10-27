<?php
namespace Archman\PaymentLib\Request\Apple;

use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;

/**
 * @link https://developer.apple.com/library/content/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html#//apple_ref/doc/uid/TP40010573-CH104-SW1 文档地址
 */
class IAPReceiptValidation implements RequestableInterface
{
    private const SANDBOX_URI = 'https://sandbox.itunes.apple.com/verifyReceipt';

    private const PRODUCTION_URI = 'https://buy.itunes.apple.com/verifyReceipt';

    private $params = [
        'receipt-data' => null,
        'password' => null,
        'exclude-old-transactions' => null,
    ];

    private $uri = null;

    public function setEnvironment(bool $isProduction): self
    {
        $this->uri = $isProduction ? self::PRODUCTION_URI : self::SANDBOX_URI;

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

    public function prepareRequest(): RequestInterface
    {
        ParameterHelper::checkRequired($this->params, ['receipt-data']);

        $uri = $this->getUri();
        $headers = ['content-type' => 'application/x-www-form-urlencoded'];
        $params = ParameterHelper::packValidParameters($this->params);
        $body = stream_for(DataParser::arrayToJson($params));

        return new Request('POST', $uri, $headers, $body);
    }

    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $data = DataParser::jsonToArray(strval($response->getBody()));

        $status = strval($data['status']);
        if ($status !== '0') {
            throw new ErrorResponseException($data['status'], null, $data);
        }

        return new GeneralResponse($data);
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }

    private function getUri(): Uri
    {
        if (!$this->uri) {
            throw new \Exception("Set Environment!!!");
        }

        return new Uri($this->uri);
    }
}