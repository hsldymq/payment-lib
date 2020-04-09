<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay;

use Archman\PaymentLib\ConfigManager\AlipayConfigInterface;
use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIResponseHandlerTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIRequestPreparationTrait;
use Archman\PaymentLib\Request\Alipay\Traits\OpenAPIParameterMakerTrait;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Response\BaseResponse;

/**
 * 支付宝商家账户当前余额查询.
 *
 * @see https://opendocs.alipay.com/apis/api_15/alipay.data.bill.balance.query
 */
class DataBillBalanceQuery implements RequestableInterface, ParameterMakerInterface
{
    use OpenAPIRequestPreparationTrait;
    use OpenAPIResponseHandlerTrait;
    use OpenAPIParameterMakerTrait;

    private const SIGN_FIELD = 'sign';
    private const CONTENT_FIELD = 'alipay_data_bill_balance_query_response';

    private AlipayConfigInterface $config;

    public function __construct(AlipayConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        return $this->makeSignedParameters('alipay.data.bill.balance.query', []);
    }

    public function send(?BaseClient $client = null): BaseResponse
    {
        return $this->handleResponse(Client::send($this));
    }
}