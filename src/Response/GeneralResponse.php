<?php
namespace Archman\PaymentLib\Response;

class GeneralResponse extends BaseResponse
{
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