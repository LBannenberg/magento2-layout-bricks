<?php

namespace Corrivate\LayoutBricks\Concern;

trait IsArrayAccessibleAndCountable
{
    /**
     * @var array<string, mixed>
     */
    public array $container = [];

    public function offsetSet($offset, $value): void
    {
        if($offset) {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * @param $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange] // Needed to placate PHPstan @ 8.2 and maintain 7.4 compatibility at the same time
    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    public function count(): int
    {
        return count($this->container);
    }
}
