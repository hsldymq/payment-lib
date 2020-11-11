<?php

namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\Config\MyCardConfigInterface;

class MyCardConfig implements MyCardConfigInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getFacServiceID(): string
    {
        return $this->config['FacServiceID'];
    }

    public function getFacKey(): string
    {
        return $this->config['FacKey'];
    }
}