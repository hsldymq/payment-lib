<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    final public function sendRequest(RequestableInterface $request): ResponseInterface
    {
        $req = $request->prepareRequest();
        $option = $request->prepareRequestOption();

        return static::doSend($req, $option);
    }

    abstract protected static function doSend(RequestInterface $request, RequestOptionInterface $option): ResponseInterface;
}