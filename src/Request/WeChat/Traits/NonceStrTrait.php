<?php

namespace Archman\PaymentLib\Request\WeChat\Traits;

trait NonceStrTrait
{
    private $nonceStr = null;

    public function setNonceStr(?string $str): self
    {
        $this->nonceStr = $str;

        return $this;
    }

    public function getNonceStr(): string
    {
        return $this->nonceStr ?? md5(microtime(true));
    }
}