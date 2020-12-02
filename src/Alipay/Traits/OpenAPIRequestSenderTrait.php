<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Traits;

use Archman\PaymentLib\Alipay\Config\OpenAPIConfigInterface;
use Archman\PaymentLib\Alipay\Helper\AESEncryption;
use Archman\PaymentLib\Alipay\Helper\OpenAPIHelper;
use Archman\PaymentLib\ClientFactoryInterface;
use Archman\PaymentLib\ClientOption;
use Archman\PaymentLib\DefaultClientFactoryProvider;
use Archman\PaymentLib\Alipay\Signature\Validator;
use Archman\PaymentLib\Exception\AlipayOpenAPIResponseException;
use Archman\PaymentLib\Exception\InvalidDataStructureException;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @property OpenAPIConfigInterface $config
 */
trait OpenAPIRequestSenderTrait
{
    public static string $baseURI = 'https://openapi.alipay.com/gateway.do';

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

        $bodyStr = strval($response->getBody());
        try {
            $data = json_decode($bodyStr, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new InvalidDataStructureException($bodyStr, [], $e->getMessage(), $e->getCode(), $e);
        }

        $contentStr = OpenAPIHelper::getResponseContent($bodyStr, self::RESPONSE_CONTENT_FIELD);
        $signature = $data['sign'] ?? null;
        // TODO 如果没有签名, 但是code为10000. 是否抛出异常?
        if ($signature) {
            (new Validator($this->config))->validateSign($signature, $this->config->getSignType(), $contentStr);
        }

        $content = $data[self::RESPONSE_CONTENT_FIELD];
        if ($this->config->isAESEncryptionEnabled()) {
             $d = AESEncryption::decrypt($content, $this->config->getAESKey());
             try {
                 $content = json_decode($d, true, 512, JSON_THROW_ON_ERROR);
             } catch (\Throwable $e) {
                 throw new InvalidDataStructureException($d, [], $e->getMessage(), $e->getCode(), $e);
             }
        }

        $code = strval($content['code'] ?? '');
        if ($code !== '10000') {
            throw new AlipayOpenAPIResponseException($content, $content['msg'] ?? '', $code);
        }

        return $content;
    }

    public function getLatestRequestInfo(): array
    {
        return $this->latestRequestInfo;
    }

    protected function doSend(ClientInterface $client): ResponseInterface
    {
        $parameters = $this->makeParameters();
        $request = new Request('POST', self::$baseURI.'?'.Query::build($parameters));

        $this->latestRequestInfo = ['request' => $request, 'response' => null];

        $response = $client->sendRequest($request);

        $this->latestRequestInfo['response'] = $request;

        return $response;
    }
}