<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Config;

interface ConfigInterface
{
    public function getAppID(): string;

    public function getPID(): string;
}