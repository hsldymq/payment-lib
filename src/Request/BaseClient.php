<?php
namespace Archman\PaymentLib\Request;

use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Response\BaseResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    final public static function sendRequest(RequestableInterface $interface): BaseResponse
    {
        $request = $interface->prepareRequest();
        $option = $interface->prepareRequestOption();
        $response = static::doSend($request, $option);

        return $interface->handleResponse($response);
    }

    abstract protected static function doSend(RequestInterface $request, RequestOptionInterface $option): ResponseInterface;
}