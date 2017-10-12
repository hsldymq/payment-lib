<?php
namespace Archman\PaymentLib\Request;

use Archman\PaymentLib\Response\BaseResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestableInterface
{
    public function prepareRequest(): RequestInterface;

    public function prepareRequestOption(): RequestOption;

    public function handleResponse(ResponseInterface $response): BaseResponse;
}
