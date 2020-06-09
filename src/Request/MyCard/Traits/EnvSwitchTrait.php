<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\MyCard\Traits;

trait EnvSwitchTrait
{
    private string $uri;

    public function setEnv(bool $isProd): self
    {
        $this->uri = $isProd ? self::PROD_URI : self::TEST_URI;

        return $this;
    }
}