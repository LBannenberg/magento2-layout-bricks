<?php

namespace Corrivate\LayoutBricks\Model;


class BrickAttributesBag
{
    public const HTML_BOOLEAN_ATTRIBUTES = [ // sourced from https://meiert.com/en/blog/boolean-attributes-of-html/
        'allowfullscreen', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled',
        'formnovalidate', 'inert', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open',
        'readonly', 'required', 'reversed', 'selected'
    ];
    public const HTML_APPENDABLE_ATTRIBUTES = ['class', 'style'];

    public function __construct(
        private array $attributes = []
    ) {
    }

    public function merge($defaultAttributes = []): static
    {
        return $this;
    }

    public function toHtml(): string
    {
        return (string) $this;
    }

    public function __toString()
    {
        $output = [];
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, self::HTML_BOOLEAN_ATTRIBUTES)) {
                if ($value) {
                    $output[] = $key.'="'.$key.'"'; // We map for example ['checked' => true] to checked="checked"
                }
            } else {
                $output[] = $key.'="'.$value.'"';
            }
        }
        return implode(' ', $output);
    }
}
