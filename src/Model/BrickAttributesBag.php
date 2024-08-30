<?php

namespace Corrivate\LayoutBricks\Model;


class BrickAttributesBag
{
    public function __construct(
        private array $attributes = [])
    {
    }

    public function merge($defaultAttributes = []): static
    {
        return $this;
    }

    public function toHtml(): string
    {
        return (string)$this;
    }

    public function __toString(){
        $output = [];
        foreach($this->attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }
        return implode(' ', $output);
    }
}
