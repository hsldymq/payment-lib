<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Config;

use Archman\PaymentLib\Alipay\Config\OpenAPI\CertConfigInterface;
use Archman\PaymentLib\Alipay\Config\OpenAPI\PKConfigInterface;
use Archman\PaymentLib\Test\Config;

class ConfigLoader
{
    public static function loadConfig(string $configName, bool $aesEnabled, bool $certEnabled): CertConfigInterface|PKConfigInterface
    {
        $configData = Config::get('alipay', 'config', $configName);

        if ($configData['isCertConfig']) {
            return new CertConfig($configData, $aesEnabled, $certEnabled);
        }

        return new PKConfig($configData, $aesEnabled);
    }
}