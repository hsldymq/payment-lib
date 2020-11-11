<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\Config\WeChatConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Request\WeChat\Enums\TarTypeEnum;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\Response\WeChat\BillResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;
use Psr\Http\Message\ResponseInterface;

/**
 * 下载资金账单.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_18&index=9
 */
class DownloadFundFlow implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/pay/downloadfundflow';

    private WeChatConfigInterface $config;

    private string $fixedSignType = 'HMAC-SHA256';

    private array $params = [
        'bill_date' => null,
        'account_type' => null,
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
        $parameters['sign_type'] = $this->fixedSignType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->fixedSignType);

        return $parameters;
    }

    public function setBillDate(\DateTime $dt): self
    {
        $this->params['bill_date'] = $dt->format('Ymd');

        return $this;
    }


    public function setAccountType(string $type): self
    {
        $this->params['account_type'] = $type;

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

    public function PrepareRequestOption(): RequestOptionInterface
    {
        return (new RequestOption())->setRootCAFilePath($this->config->getRootCAPath())
            ->setSSLKeyFilePath($this->config->getSSLKeyPath())
            ->setSSLKeyPassword($this->config->getSSLKeyPassword())
            ->setSSLCertFilePath($this->config->getClientCertPath())
            ->setSSLCertPassword($this->config->getClientCertPassword());
    }

    /**
     * @param ResponseInterface $response
     *
     * @return BillResponse
     * @throws
     */
    private function parseBillResponse(ResponseInterface $response): BillResponse
    {
        $body = strval($response->getBody());
        if (strpos($body, '<xml>') === 0) {
            $this->parseXMLDataAndCheck($body);
        }

        if ($this->params['tar_type'] === TarTypeEnum::GZIP) {
            $body = gzdecode($body);
        }

        return new BillResponse($body);
    }
}