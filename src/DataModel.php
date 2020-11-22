<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Archman\PaymentLib\Exception\ContextualException;

class DataModel implements \ArrayAccess
{
    private static array $converters = [
        'default' => [ModelDataConverter::class, 'defaultConvert'],
    ];

    private array $data = [];

    final public function __construct(array $data)
    {
        $this->data = $data;
        $this->assignProps();
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
        $obj = new \ReflectionObject($this);
        foreach ($obj->getProperties() as $prop) {
            $attr = $prop->getAttributes('DataModel')[0] ?? null;
            if (!$attr) {
                continue;
            }
            $fieldName = $attr->getArguments()[0] ?? null;
            if (!$fieldName || !isset($this->data[$fieldName])) {
                continue;
            }

            $typeStr = strval($prop->getType());
            if ($typeStr) {
                // TODO
//                $converterName = 'default';
//                $attr = $prop->getAttributes('DataModelConverter')[0] ?? null;
//                if ($attr && ($name = $attr->getArguments()[0] ?? null)) {
//                    $converterName = $name;
//                }
            }

            $value = $this->data[$fieldName];
            $prop->setValue($this, $value);
        }
    }
}