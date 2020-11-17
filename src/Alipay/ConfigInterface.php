<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay;

interface ConfigInterface
{
    public function getAppID(): string;

    public function getPartnerID(): string;
}