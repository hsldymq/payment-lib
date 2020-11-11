<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Config;

interface NewebPayConfigInterface
{
    public function getMerchantID(): string;

    public function getHashKey(): string;

    public function getHashIV(): string;
}