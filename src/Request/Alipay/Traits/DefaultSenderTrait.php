<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\Alipay\Traits;

use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Response\BaseResponse;

trait DefaultSenderTrait
{
    /**
     * 发送请求.
     *
     * @param BaseClient|null $client
     *
     * @return BaseResponse
     * @throws
     */
    public function send(?BaseClient $client = null): BaseResponse
    {
        return $client ? $client->sendRequest($this) : Client::sendRequest($this);
    }
}
