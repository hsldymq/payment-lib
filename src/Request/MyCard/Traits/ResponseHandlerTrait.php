<?php

namespace Archman\PaymentLib\Request\MyCard\Traits;

use Archman\PaymentLib\Exception\ErrorResponseException;
use Archman\PaymentLib\Request\DataConverter;
use Archman\PaymentLib\Response\BaseResponse;
use Archman\PaymentLib\Response\GeneralResponse;
use Psr\Http\Message\ResponseInterface;

trait ResponseHandlerTrait
{
    public function handleResponse(ResponseInterface $response): BaseResponse
    {
        $body = strval($response->getBody());

        try {
            $data = DataConverter::jsonToArray($body);
        } catch (\Throwable $e) {
            throw new ErrorResponseException(
                $e->getCode(),
                $e->getMessage(),
                null,
                $e->getCode(),
                $e->getMessage(),
                $e
            );
        }

        if ($data['ReturnCode'] !== '1') {
            throw new ErrorResponseException($data['ReturnCode'], $data['ReturnMsg'], $data);
        }

        return new GeneralResponse($data);
    }
}