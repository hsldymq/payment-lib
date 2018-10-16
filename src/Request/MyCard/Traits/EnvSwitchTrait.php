<?php

namespace Archman\PaymentLib\Request\MyCard\Traits;

trait EnvSwitchTrait
{
    private $uri;

    public function setEnv(bool $isProd): self
    {
        $this->uri = $isProd ? self::PROD_URI : self::TEST_URI;

        return $this;
    }
}