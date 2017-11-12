<?php
namespace Archman\PaymentLib\Request;

interface ParameterMakerInterface
{
    public function makeParameters(bool $withSign = true): array;
}