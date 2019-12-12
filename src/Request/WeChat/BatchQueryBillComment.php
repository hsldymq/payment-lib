<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\DataParser;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Response\WeChat\BillCommentResponse;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;
use Psr\Http\Message\ResponseInterface;

/**
 * 拉取订单评价数据.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_17&index=11
 */
class BatchQueryBillComment implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;

    private string $fixedSignType = 'HMAC-SHA256';

    private const URI = 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment';

    private WeChatConfigInterface $config;

    private array $params = [
        'begin_time' => null,
        'end_time' => null,
        'offset' => null,
        'limit' => null,
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
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->fixedSignType, ['limit', 'sign_type']);

        return $parameters;
    }

    public function setBeginTime(\DateTime $dt): self
    {
        $this->params['begin_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setEndTime(\DateTime $dt): self
    {
        $this->params['end_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    public function setLimit(?int $limit): self
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    public function send(?BaseClient $client = null): BillCommentResponse
    {
        $response = $client ? $client->sendRequest($this) : Client::send($this);

        return $this->handleResponse($response);
    }

    public function prepareRequestOption(): RequestOptionInterface
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
     * @return BillCommentResponse
     * @throws
     */
    private function handleResponse(ResponseInterface $response): BillCommentResponse
    {
        $rawBody = strval($response->getBody());

        $errCode = $errMsg = $data = null;
        if (strpos($rawBody, '<xml>') === 0) {
            $data = DataParser::xmlToArray($rawBody);
            if (strtoupper($data['return_code']) !== 'SUCCESS') {
                $errCode = $data['return_code'];
                $errMsg = $data['return_msg'];
            } elseif (strtoupper($data['result_code']) !== 'SUCCESS') {
                $errCode = $data['err_code'];
            }
        }
        if ($errCode) {
            throw new ErrorResponseException($errCode, $errMsg, $data);
        }

        return new BillCommentResponse($rawBody);
    }
}