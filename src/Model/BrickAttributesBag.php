<?php

namespace Corrivate\LayoutBricks\Model;


class BrickAttributesBag
{
    public function __construct(
        private array $attributes = [])
    {
    }

    public function merge(): static
    {
        return $this;
    }

    public function toHtml(): string
    {
        return (string)$this;
    }

    public function __toString(){
        return "test";
    }
}
