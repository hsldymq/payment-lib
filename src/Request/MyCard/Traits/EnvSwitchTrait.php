<?php

namespace Archman\PaymentLib\Request\MyCard\Traits;

trait EnvSwitchTrait
{
    private $uri;

    public function setEnv(bool $isTest): self
    {
        $this->uri = $isTest ? self::TEST_URI : self::PROD_URI;

        return $this;
    }
}