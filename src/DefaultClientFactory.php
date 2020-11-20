<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

class DefaultClientFactory implements ClientFactoryInterface
{
    public function makeClient(ClientOption $option): ClientInterface
    {
        /** TODO */
        return new Client();
    }
}
