<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Traits;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Helper\OpenAPIHelper;
use Archman\PaymentLib\ClientFactoryInterface;
use Archman\PaymentLib\ClientOption;
use Archman\PaymentLib\DefaultClientFactoryProvider;
use Archman\PaymentLib\Alipay\Signature\Validator;
use Archman\PaymentLib\Exception\AlipayOpenAPIResponseException;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @property OpenAPIConfigInterface $config
 */
trait OpenAPIRequestSenderTrait
{
    private array $latestRequestInfo = [
        'request' => null,
        'response' => null,
    ];

    public function sendRequest(?ClientFactoryInterface $clientFactory = null)
    {
        if (!$clientFactory) {
            $clientFactory = DefaultClientFactoryProvider::getFactory();
        }

        $client = $clientFactory->makeClient(new ClientOption());

        $response = $this->doSend($client);

        $bodyStr =  strval($response->getBody());
        $contentStr = OpenAPIHelper::getResponseContent($bodyStr, self::RESPONSE_CONTENT_FIELD);
        $data = json_decode($bodyStr, true, 512, JSON_THROW_ON_ERROR);
        $signature = $data['sign'];

        (new Validator($this->config))->validateSign($signature, $this->config->getSignType(), $contentStr);

        $content = $data[self::RESPONSE_CONTENT_FIELD];
        if (($content['code'] ?? '') !== '10000') {
            throw new AlipayOpenAPIResponseException($content, $content['msg'], $content['code']);
        }

        return $content;
    }

    private function doSend(ClientInterface $client): ResponseInterface
    {
        $parameters = $this->makeParameters();
        print_r($parameters);
        $request = new Request('POST','https://openapi.alipay.com/gateway.do?'.Query::build($parameters));

        $this->latestRequestInfo = ['request' => $request, 'response' => null];

        $response = $client->sendRequest($request);

        $this->latestRequestInfo['response'] = $request;

        return $response;
    }

    public function getLatestRequestInfo(): array
    {
        return $this->latestRequestInfo;
    }
}