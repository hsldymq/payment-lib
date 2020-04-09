<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat\Traits;

trait NonceStrTrait
{
    /** @var string|null  */
    private $nonceStr = null;

    public function setNonceStr(?string $str): self
    {
        $this->nonceStr = $str;

        return $this;
    }

    public function getNonceStr(): string
    {
        return $this->nonceStr ?? md5(strval(microtime(true)));
    }
}