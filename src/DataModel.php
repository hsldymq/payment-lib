<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Archman\PaymentLib\Exception\ContextualException;

class DataModel implements \ArrayAccess
{
    private array $data = [];

    final public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        throw new ContextualException(['offset' => $offset], 'set immutable data model');
    }

    public function offsetUnset($offset)
    {
        throw new ContextualException(['offset' => $offset], 'unset immutable data model');
    }

    private function assignProps()
    {
        // TODO
    }
}