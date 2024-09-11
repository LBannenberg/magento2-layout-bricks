<?php

namespace Corrivate\LayoutBricks\Model;


use Corrivate\LayoutBricks\Concern\IsArrayAccessible;

class BrickPropsBag implements \ArrayAccess
{
    use IsArrayAccessible;
    public function __construct(
        array $props = []
    ) {
        $this->container = $props;
    }

    public function merge($defaults = []): BrickPropsBag
    {
        $result = $defaults;
        foreach($this->container as $key => $value) {
            $result[$key] = $value;
        }
        $this->container = $result;
        return $this;
    }
}
