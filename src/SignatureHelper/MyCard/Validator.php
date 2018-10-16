<?php

namespace Archman\PaymentLib\SignatureHelper\MyCard;

use Archman\PaymentLib\ConfigManager\MyCardConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

class Validator
{
    private $config;

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $hash
     * @param array $data
     *
     * @return bool
     * @throws SignatureException
     */
    public function validate(string $hash, array $data): bool
    {
        $raw = $data;

        unset($data['Hash']);
        unset($data['ReturnMsg']);
        $result = $hash === HashHelper::makeHash($data);


        if (!$result) {
            throw (new SignatureException('Failed To Validate MyCard Hash.'))->setData($raw)->setSign($hash);
        }

        return true;
    }
}