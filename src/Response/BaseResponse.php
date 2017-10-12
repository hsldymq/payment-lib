<?php
namespace Archman\PaymentLib\Response;

abstract class BaseResponse implements \ArrayAccess
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}