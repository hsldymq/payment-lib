<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager;

interface MyCardConfigInterface
{
    public function getFacServiceID(): string;

    public function getFacKey(): string;
}