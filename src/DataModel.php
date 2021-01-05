<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Archman\PaymentLib\Exception\ContextualException;
use JetBrains\PhpStorm\Pure;

class DataModel extends \IteratorIterator implements \ArrayAccess, \Iterator, \Countable
{
    final public function __construct(private array $data)
    {
        parent::__construct(new \ArrayIterator($this->data));
        $this->assignProps();
    }

    #[Pure]
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    #[Pure]
    public function offsetGet($offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        throw new ContextualException(['offset' => $offset], 'set immutable data model');
    }

    public function offsetUnset($offset): void
    {
        throw new ContextualException(['offset' => $offset], 'unset immutable data model');
    }

    #[Pure]
    public function count(): int
    {
        return count($this->data);
    }

    private function assignProps(): void
    {
        $obj = new \ReflectionObject($this);
        foreach ($obj->getProperties() as $prop) {
            $attr = $prop->getAttributes('DataField')[0] ?? null;
            if (!$attr) {
                continue;
            }
            $fieldName = $attr->getArguments()[0] ?? null;
            if (!$fieldName || !isset($this->data[$fieldName])) {
                continue;
            }

            $value = $this->data[$fieldName];

            $converterAttr = $prop->getAttributes('DataConverter')[0] ?? null;
            if ($converterAttr) {
                $converterName = strval($converterAttr->getArguments()[0] ?? '');
                $converter = DataConverterFactory::getConverter($converterName);
                $types = $this->getPropTypes($prop);
                if ($types) {
                    $value = $converter($value, $types);
                }
            }

            $prop->setValue($this, $value);
        }
    }

    private function getPropTypes(\ReflectionProperty $prop): array
    {
        $types = [];
        $type = $prop->getType();
        if ($type instanceof \ReflectionNamedType) {
            $types[] = $type->getName();
        } else if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $eachType) {
                $types[] = $eachType->getName();
            }
        }

        return $types;
    }
}