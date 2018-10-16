<?php

namespace Archman\PaymentLib\Test\Config;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;

class MyCardConfig implements MyCardConfigInterface
{
    public function getFacServiceID(): string
    {
        return 'test_service_id';
    }

    public function getFacKey(): string
    {
        return 'test_fac_key';
    }
}