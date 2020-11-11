<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\MyCard;

use Archman\PaymentLib\Config\MyCardConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

class Validator
{
    private MyCardConfigInterface $config;

    public function __construct(MyCardConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * 验证支付完成回调数据Hash.
     *
     * @param string $hash
     * @param array $data
     *
     * @return bool
     * @throws SignatureException
     */
    public function validatePayResultHash(string $hash, array $data): bool
    {
        $raw = $data;

        $fields = [
            'ReturnCode',
            'PayResult',
            'FacTradeSeq',
            'PaymentType',
            'Amount',
            'Currency',
            'MyCardTradeNo',
            'MyCardType',
            'PromoCode',
        ];
        $data = [];
        foreach ($fields as $key) {
            if (isset($raw[$key])) {
                $data[$key] = $raw[$key];
            }
        }

        $data['FacKey'] = $this->config->getFacKey();
        $sign = HashHelper::makeHash($data);
        $result = $hash === $sign;

        if (!$result) {
            throw (new SignatureException('Failed To Validate MyCard Hash.'))->setData($raw)->setSign($hash);
        }

        return true;
    }
}