<?php
namespace Archman\PaymentLib\Request;

use Archman\PaymentLib\Response\BaseResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    private static $implement = Client::class;

    final public static function sendRequest(RequestableInterface $interface): BaseResponse
    {
        $request = $interface->prepareRequest();
        $option = $interface->prepareRequestOption();
        $response = static::doSend($request, $option);

        return $interface->handleResponse($response);
    }

    /**
     * @param string $class
     * @usage BaseClient::registerImplement(SomeClientImpl::class);
     */
    final public static function registerImplement(string $class)
    {
        self::$implement = $class;
    }

    /**
     * @return BaseClient
     */
    final public static function getImplement()
    {
        return self::$implement;
    }

    abstract protected static function doSend(RequestInterface $request, RequestOptionInterface $option): ResponseInterface;
}