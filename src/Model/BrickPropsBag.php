<?php

namespace Corrivate\LayoutBricks\Model;


use Corrivate\LayoutBricks\Concern\IsArrayAccessibleAndCountable;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class BrickPropsBag implements \ArrayAccess, \Countable
{
    use IsArrayAccessibleAndCountable;

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
    public function default(array $defaults = []): BrickPropsBag
    {
        $result = $defaults;
        foreach ($this->container as $key => $value) {
            $result[$key] = $value;
        }
        $this->container = $result;
        return $this;
    }
}
