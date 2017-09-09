<?php
namespace Archman\PaymentLib\ConfigManager;

interface WeixinConfigInterface
{
    public function getAppID(): string;
}