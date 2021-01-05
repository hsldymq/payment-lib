<?php

declare(strict_types=1);

namespace Archman\PaymentLib;

use Archman\PaymentLib\Exception\ContextualException;
use JetBrains\PhpStorm\Pure;

class DataModel implements \ArrayAccess, \Iterator, \Countable
{
    private array $data;
    private array $keys;
    private int $numKeys;
    private int $position = 0;

    final public function __construct(array $data)
    {
        $this->data = $data;
        $this->numKeys = count($data);
        $this->keys = array_keys($data);
        $this->assignProps();
    }

    #[Pure]
    public function current(): mixed
    {
        if ($this->position >= $this->numKeys) {
            return null;
        }

        $key = $this->keys[$this->position];
        return $this->data[$key];
    }

    public function next(): void
    {
        $this->position += ($this->position < $this->numKeys ? 1 : 0);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    #[Pure]
    public function key(): string|float|int|bool|null
    {
        return $this->position < $this->numKeys ? $this->keys[$this->position] : null;
    }

    #[Pure]
    public function valid(): bool
    {
        return $this->position >= $this->numKeys;
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

    public function count(): int
    {
        return $this->numKeys;
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