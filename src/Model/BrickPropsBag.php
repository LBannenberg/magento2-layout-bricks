<?php

namespace Corrivate\LayoutBricks\Model;


use Corrivate\LayoutBricks\Concern\IsArrayAccessible;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class BrickPropsBag implements \ArrayAccess
{
    use IsArrayAccessible;

    /**
     * @param  array<string, mixed>  $props
     */
    public function __construct(
        array $props = []
    ) {
        $this->container = $props;
    }

    /**
     * @param array<string, mixed> $defaults
     */
    public function merge(array $defaults = []): BrickPropsBag
    {
        $result = $defaults;
        foreach ($this->container as $key => $value) {
            $result[$key] = $value;
        }
        $this->container = $result;
        return $this;
    }
}
