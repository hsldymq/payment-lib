<?php

declare(strict_types=1);

namespace Archman\PaymentLib\ConfigManager;

interface NewebPayConfigInterface
{
    public function getMerchantID(): string;

    public function getHashKey(): string;

    public function getHashIV(): string;
}