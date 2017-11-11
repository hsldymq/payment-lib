<?php
namespace Archman\PaymentLib\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client extends BaseClient
{
    protected static function doSend(RequestInterface $request, RequestOptionInterface $option): ResponseInterface
    {
        // TODO
    }
}