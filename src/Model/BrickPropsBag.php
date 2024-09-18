<?php declare(strict_types=1);

namespace Corrivate\LayoutBricks\Model;


use Corrivate\LayoutBricks\Concern\IsArrayAccessibleAndCountable;
use Corrivate\LayoutBricks\Exception\PropHasUnexpectedTypeException;
use Corrivate\LayoutBricks\Exception\PropIsMissingException;
use Corrivate\LayoutBricks\Exception\PropExpectedTypeStringInvalidException;

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
     * @param  array<string, mixed>  $defaults
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


    /**
     * @param  array<string, string>  $expectations
     * @throws PropExpectedTypeStringInvalidException
     * @throws PropIsMissingException
     * @throws PropHasUnexpectedTypeException
     */
    public function expect(array $expectations = []): self
    {
        foreach ($expectations as $propName => $acceptedTypesString) {
            if (substr($acceptedTypesString, 0, 1) === '?' && strpos($acceptedTypesString, '|') !== false) {
                throw new PropExpectedTypeStringInvalidException(
                    "Cannot use '?' to start a prop's type-string AND use |; use |null instead."
                );
            }

            $nullable = false;
            if(substr($acceptedTypesString, 0, 1) === '?') {
                $nullable = true;
                $acceptedTypesString = substr($acceptedTypesString, 1);
            }
            $acceptedTypes = explode('|', $acceptedTypesString);
            $nullable = $nullable || in_array('null', $acceptedTypes);

            if ($nullable
                && (!in_array($propName, array_keys($this->container))
                    || $this->container[$propName] === null)
            ) {
                $this->container[$propName] = null;
                continue;
            }

            if(!isset($this->container[$propName])) {
                throw new PropIsMissingException(
                    "Expected prop '$propName' with type(s) '$acceptedTypesString' but did not receive it."
                );
            }

            $subject = $this->container[$propName];

            // See if we can match to any accept type;
            // We need continue 3 because the switch statement also counts as a loop context; see
            // https://www.php.net/manual/en/control-structures.continue.php
            /** @var string[] $acceptedTypes */
            foreach ($acceptedTypes as $acceptedType) {
                switch ($acceptedType) {
                    case 'string':
                        if (is_string($subject) || $subject instanceof \Magento\Framework\Phrase) {
                            $this->container[$propName] = (string) $subject;
                            continue 3;
                        }
                        break;
                    case 'int':
                        if (is_int($subject)) {
                            continue 3;
                        }
                        break;
                    case 'float':
                        if (is_float($subject) || gettype($subject) == 'double') {
                            $this->container[$propName] = (float)$subject;
                            continue 3;
                        }
                        break;
                    case 'bool':
                        if (is_bool($subject)) {
                            continue 3;
                        }
                        break;
                    case 'array':
                        if (is_array($subject)) {
                            continue 3;
                        }
                        break;
                    default:
                        if ($subject instanceof $acceptedType) {
                            continue 3;
                        }
                }
            }

            // Could not match to any accepted type
            $actualType = gettype($subject) == 'object'
                ? get_class($subject)
                : gettype($subject);
            // Undo removing '?' prefix, if needed
            $acceptedTypesString = $nullable && count($acceptedTypes) == 1
                ? '?'.$acceptedTypesString
                : $acceptedTypesString;
            throw new PropHasUnexpectedTypeException(
                "Prop '$propName' has unexpected type '$actualType', expected '$acceptedTypesString'"
            );
        }
        return $this;
    }
}
