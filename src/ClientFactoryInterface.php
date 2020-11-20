<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Psr\Http\Client\ClientInterface;

interface ClientFactoryInterface
{
    public function makeClient(ClientOption $option): ClientInterface;
}
