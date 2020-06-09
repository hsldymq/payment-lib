<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request;

use Archman\PaymentLib\Response\BaseResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestableInterface
{
    public function send(?BaseClient $client = null);

    public function prepareRequest(): RequestInterface;

    public function prepareRequestOption(): RequestOptionInterface;
}
