<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\MyCard\Traits;

use Archman\PaymentLib\Request\BaseClient;
use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;

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
        $resp = $client ? $client->sendRequest($this) : Client::send($this);

        return new GeneralResponse($this->handleResponse($resp));
    }
}
