<?php

namespace Archman\PaymentLib\SignatureHelper\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;

class Generator
{
    private $config;

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeHash(array $data, array $exclude = []): string
    {
        foreach ($exclude as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }
        $data['FacKey'] = $this->config->getFacKey();
        $hash = HashHelper::makeHash($data);

        return $hash;
    }
}
