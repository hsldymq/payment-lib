<?php
namespace Archman\PaymentLib\ConfigManager;

interface AlipayConfigInterface
{
    public function getAppID(): string;

    public function getPartnerID(): string;

    public function getAlipayPublicKey(?string $algo = null): string;

    public function getAppPrivateKey(?string $algo = null): string;

    public function getAppPublicKey(?string $algo = null): string;

    public function getRootCAPath(?string $algo = null): string;
}