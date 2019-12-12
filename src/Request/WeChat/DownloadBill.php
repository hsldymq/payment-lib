<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\InternalErrorException;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Enums\TarTypeEnum;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\WeChat\BillResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;
use Psr\Http\Message\ResponseInterface;

/**
 * 下载对账单.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_6&index=8
 */
class DownloadBill implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    private WeChatConfigInterface $config;

    private array $params = [
        'device_info' => null,
        'bill_date' => null,
        'bill_type' => null,
        'tar_type' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->config->getSignType();
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->config->getSignType());

        return $parameters;
    }

    public function setBillDate(\DateTime $dt): self
    {
        $this->params['bill_date'] = $dt->format('Ymd');

        return $this;
    }

    public function setBillType(string $type): self
    {
        $this->params['bill_type'] = $type;

        return $this;
    }

    public function setTarType(?string $type): self
    {
        $this->params['tar_type'] = $type;

        return $this;
    }

    public function send(?BaseClient $client = null): BillResponse
    {
        $response = $client ? $client->sendRequest($this) : Client::send($this);

        return $this->parseBillResponse($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return BillResponse
     * @throws
     */
    private function parseBillResponse(ResponseInterface $response): BillResponse
    {
        $body = $response->getBody()->getContents();

        if (strpos($body, '<xml>') === 0) {
            $this->parseXMLDataAndCheck($body);
        }

        if ($this->params['tar_type'] === TarTypeEnum::GZIP) {
            $body = gzdecode($body);
            if ($body === false) {
                throw new InternalErrorException(['data' => $body], 'gzip decoding bill data');
            }
        }

        return new BillResponse($body);
    }
}