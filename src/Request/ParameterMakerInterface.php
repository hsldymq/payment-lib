<?php

namespace Archman\PaymentLib\Request;

interface ParameterMakerInterface
{
    public function makeParameters(): array;
}