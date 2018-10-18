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

    public function __set($name, $value)
    {
        // Immutable
        return;
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        // Immutable
        return;
    }

    public function offsetUnset($offset)
    {
        // Immutable
        return;
    }
}