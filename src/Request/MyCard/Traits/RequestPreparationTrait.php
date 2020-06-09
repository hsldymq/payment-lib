<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\MyCard\Traits;

use Archman\PaymentLib\Request\RequestOption;
use Archman\PaymentLib\Request\RequestOptionInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\stream_for;

trait RequestPreparationTrait
{
    use EnvSwitchTrait;

    public function prepareRequest(): RequestInterface
    {
        if (!$this->uri) {
            throw new \Exception("Unknown Request Uri, Invoke setEnv Method!");
        }

        $parameters = $this->makeParameters();
        $request = new Request('POST', $this->uri, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], stream_for(build_query($parameters)));

        return $request;
    }

    public function prepareRequestOption(): RequestOptionInterface
    {
        return new RequestOption();
    }
}