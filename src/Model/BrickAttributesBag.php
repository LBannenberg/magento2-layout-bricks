<?php

namespace Corrivate\LayoutBricks\Model;


readonly class BrickAttributesBag
{
    public const HTML_BOOLEAN_ATTRIBUTES = [ // sourced from https://meiert.com/en/blog/boolean-attributes-of-html/
        'allowfullscreen', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled',
        'formnovalidate', 'inert', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open',
        'readonly', 'required', 'reversed', 'selected'
    ];

    public function __construct(
        private array $attributes = []
    ) {
    }

    public function merge($defaultAttributes = []): static
    {
        $result = $defaultAttributes;
        foreach($this->attributes as $key => $value) {
            if ($key === 'class') {
                $result['class'] = isset($result['class'])
                    ? $result['class'] . ' ' . $value // Specific classes added after default, so that they can override
                    : $value;
            } elseif ($key === 'style') {
                if(empty($result['style'])) {
                    $result['style'] = $this->endWith($value, ';');
                    continue;
                }
                $result['style'] = $this->endWith($result['style'], ';'). ' ' . $this->endWith($value, ';');
            } else {
                $result[$key] = $value;
            }
        }
        return new BrickAttributesBag($result);
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

    private function endWith(string $value, string $end): string
    {
        $value = trim($value);
        if(strlen($value) == 0) {
            return '';
        }

        return substr($value, -1) == $end
            ? $value
            : $value . $end;
    }
}
