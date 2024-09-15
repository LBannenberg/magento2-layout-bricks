<?php

namespace Corrivate\LayoutBricks\Model;


use Corrivate\LayoutBricks\Concern\IsArrayAccessibleAndCountable;

/**
 * @implements \ArrayAccess<string, string>
 */
class BrickAttributesBag implements \ArrayAccess, \Countable
{
    use IsArrayAccessibleAndCountable;

    public const HTML_BOOLEAN_ATTRIBUTES = [ // sourced from https://meiert.com/en/blog/boolean-attributes-of-html/
        'allowfullscreen', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled',
        'formnovalidate', 'inert', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open',
        'readonly', 'required', 'reversed', 'selected'
    ];

    /**
     * @param  array<string|int, string|bool>  $attributes
     */
    public function __construct(
        array $attributes = []
    ) {
        $attributes = $this->sanitizeBooleanAttributes($attributes);
        $this->container = $attributes;
    }

    /**
     * @param  array<string|int, string|bool>  $defaults
     * @return $this
     */
    public function merge(array $defaults = []): BrickAttributesBag
    {
        $defaults = $this->sanitizeBooleanAttributes($defaults);
        $result = $defaults;
        foreach ($this->container as $key => $value) {
            if ($key === 'class') {
                $result['class'] = isset($result['class'])
                    ? $result['class'].' '.$value // Specific classes added after default, so that they can override
                    : $value;
            } elseif ($key === 'style') {
                if (empty($result['style'])) {
                    $result['style'] = $this->endWith($value, ';');
                    continue;
                }
                $result['style'] = $this->endWith($result['style'], ';').' '.$this->endWith($value, ';');
            } else {
                $result[$key] = $value;
            }
        }
        $this->container = $result;
        return $this;
    }

    public function toHtml(): string
    {
        return (string) $this;
    }

    public function __toString()
    {
        $output = [];
        foreach ($this->container as $key => $value) {
            // Render only truthy boolean attributes
            if (in_array($key, self::HTML_BOOLEAN_ATTRIBUTES)) {
                if($value) {
                    $output[] = $key; // We map for example ['checked' => true] to checked
                }
                continue;
            }

            // Non-Boolean attributes
            $output[] = $key.'="'.$value.'"';
        }
        return implode(' ', $output);
    }

    private function endWith(string $value, string $end): string
    {
        $value = trim($value);
        if (strlen($value) == 0) {
            return '';
        }

        return substr($value, -1) == $end
            ? $value
            : $value.$end;
    }

    /**
     * @param  array<string|int, string|bool>  $attributes
     * @return array<string, string|bool>
     */
    private function sanitizeBooleanAttributes(array $attributes): array
    {
        $result = [];
        foreach($attributes as $key => $value) {
            if(is_int($key) && in_array($value, self::HTML_BOOLEAN_ATTRIBUTES)) {
                $result[(string)$value] = true;
                continue;
            }
            if(in_array($key, self::HTML_BOOLEAN_ATTRIBUTES)) {
                $result[(string)$key] = (bool)$value;
                continue;
            }
            $result[(string)$key] = $value;
        }
        return $result;
    }
}
