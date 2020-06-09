<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request;

interface ParameterMakerInterface
{
    public function makeParameters(): array;
}