<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class SignatureException extends \Exception
{
    private $data;

    private $sign;

    protected $message = 'Failed To Validate Signature';

    public function getData()
    {
        return $this->data;
    }

    public function getSign(): ?string
    {
        return $this->sign;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setSign(string $sign): self
    {
        $this->sign = $sign;

        return $this;
    }
}