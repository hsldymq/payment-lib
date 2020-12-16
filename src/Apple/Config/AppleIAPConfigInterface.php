<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Apple\Config;

interface AppleIAPConfigInterface
{
    public function getPassword(): ?string;
}