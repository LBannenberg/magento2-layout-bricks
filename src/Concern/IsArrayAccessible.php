<?php

namespace Corrivate\LayoutBricks\Concern;

trait IsArrayAccessible
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

    public function offsetGet($offset): mixed
    {
        return $this->container[$offset];
    }
}
