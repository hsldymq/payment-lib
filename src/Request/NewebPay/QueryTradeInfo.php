<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\NewebPay;

use Archman\PaymentLib\Config\NewebPayConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\stream_for;

class QueryTradeInfo implements RequestableInterface, ParameterMakerInterface
{
    private NewebPayConfigInterface $config;

    private array $params = [
        'MerchantID' => '',
        'Version' => '1.1',
        'RespondType' => 'JSON',
        'CheckValue' => '',
        'TimeStamp' => '',
        'MerchantOrderNo' => '',
        'Amt' => 0,
    ];

    public function __construct(NewebPayConfigInterface $config)
    {
        $this->config = $config;
        $this->params['MerchantID'] = $config->getMerchantID();
    }


    public function prepareRequest(): RequestInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', 'https://core.spgateway.com/API/QueryTradeInfo', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], stream_for(build_query($parameters)));

        return $request;
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }

    public function send(?BaseClient $client = null)
    {
        return Client::send($this);
    }

    public function makeParameters(): array
    {
        $this->params['TimeStamp'] = strval(time());
        $checkCodeStr = "IV={$this->config->getHashIV()}&Amt={$this->params['Amt']}&MerchantID={$this->params['MerchantID']}&MerchantOrderNo={$this->params['MerchantOrderNo']}&Key={$this->config->getHashKey()}";
        $this->params['CheckValue'] = strtoupper(hash("sha256", $checkCodeStr));

        return $this->params;
    }

    public function setMerchantOrderNo(string $no): self
    {
        $this->params['MerchantOrderNo'] = $no;

        return $this;
    }

    public function setAmount(int $amount): self
    {
        if ($amount > 0) {
            $amount = intval(bcdiv(strval($amount), '100', 0));
        }
        $this->params['Amt'] = $amount;

        return $this;
    }
}
