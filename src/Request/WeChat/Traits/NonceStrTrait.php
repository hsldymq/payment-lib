<?php
namespace Archman\PaymentLib\Request\WeChat\Traits;

trait NonceStrTrait
{
    public function getNonceStr(): string
    {
        return md5(microtime(true));
    }
}