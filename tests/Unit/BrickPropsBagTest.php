<?php declare(strict_types=1);

namespace Corrivate\RestApiLogger\Tests\Unit;

use Corrivate\LayoutBricks\Exception\BrickException;
use Corrivate\LayoutBricks\Exception\PropExpectedTypeStringInvalidException;
use Corrivate\LayoutBricks\Exception\PropHasUnexpectedTypeException;
use Corrivate\LayoutBricks\Exception\PropIsMissingException;
use Corrivate\LayoutBricks\Model\BrickAttributesBag;
use Corrivate\LayoutBricks\Model\BrickPropsBag;
use PHPUnit\Framework\TestCase;

class BrickPropsBagTest extends TestCase
{
    public function testThatEmptyPropsBagCanBeInstantiated()
    {
        // ARRANGE
        $bag = new BrickPropsBag();

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame(0, count($bag));
    }

    public function testThatInitialPropsCanBeAccessed()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'bar']);

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame('bar', $bag['foo']);
    }

    public function testThatDefaultPropsCanBeMergedAndAccessed()
    {
        // ARRANGE
        $bag = new BrickPropsBag();

        // ACT
        $bag->default(['foo' => 'bar']);

        // ASSERT
        $this->assertSame('bar', $bag['foo']);
    }

    public function testThatInjectedPropsReplaceDefaultProps()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'baz']);

        // ACT
        $bag->default(['foo' => 'bar']);

        // ASSERT
        $this->assertSame('baz', $bag['foo']);
    }


    public function testThatBothDefaultAndInjectedPropsCanBeUsedTogether()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'baz']);

        // ACT
        $bag->default(['bar' => 'bar']);

        // ASSERT
        $this->assertSame('baz', $bag['foo']);
        $this->assertSame('bar', $bag['bar']);
    }


    public function testThatPropBagAcceptsMetExpectations()
    {
        // ARRANGE
        $bag = new BrickPropsBag([
            'block_id' => 'test_block',
            'label' => __('OK'),
            'count' => 3,
            'price' => 3.14
        ]);

        // EXPECT
        $this->expectNotToPerformAssertions();

        // ACT
        $bag->expect([
            'block_id' => 'string',
            'label' => 'string',
            'count' => 'int',
            'price' => 'float',
            'option' => '?string'
        ]);
    }


    public function testThatMissedExpectedPropsAreDetected()
    {
        // ARRANGE
        $bag = new BrickPropsBag();

        // EXPECT
        $this->expectException(PropIsMissingException::class);
        $this->expectExceptionMessage("Expected prop 'price' with type(s) 'float' but did not receive it.");

        // ACT
        $bag->expect(['price' => 'float']);
    }


    public function testThatIncorrectlyTypedPropsAreDetected()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['price' => '1.5']);

        // EXPECT
        $this->expectException(PropHasUnexpectedTypeException::class);
        $this->expectExceptionMessage("Prop 'price' has unexpected type 'string', expected 'float'");

        // ACT
        $bag->expect(['price' => 'float']);
    }

    public function testThatNullablePropsAreStillCheckedIfPresent()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['price' => '1.5']); // string, not float!

        // EXPECT
        $this->expectException(PropHasUnexpectedTypeException::class);
        $this->expectExceptionMessage("Prop 'price' has unexpected type 'string', expected '?float'");

        // ACT
        $bag->expect(['price' => '?float']);
    }


    public function testThatNullablePropsAreNotCheckedIfAbsent()
    {
        // ARRANGE
        $bag = new BrickPropsBag([]);

        // ACT
        $bag->expect(['price' => '?float']);

        $this->assertSame(null, $bag['price']);
    }

    public function testThatNullablePropsAreAcceptedIfPresentAndCorrect()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['price' => 1.5]);

        // ACT
        $bag->expect(['price' => '?float']);

        $this->assertSame(1.5, $bag['price']);
    }


    public function testThatPropExpectationsAcceptChildClassesAndImplementation()
    {
        // ARRANGE
        $bag = new BrickPropsBag([
            'exception' => new PropIsMissingException(),
            'array' => new BrickAttributesBag()
        ]);

        // EXPECT
        $this->expectNotToPerformAssertions();

        // ACT
        $bag->expect([
            'exception' => BrickException::class, // parent class
            'array' => \ArrayAccess::class // interface
        ]);
    }


    public function testThatPropStringExpectationsAcceptPhrases()
    {
        // ARRANGE
        $bag = new BrickPropsBag(['label' => __('OK')]);

        // EXPECT
        $this->expectNotToPerformAssertions();

        // ACT
        $bag->expect(['label' => 'string']);
    }


    public function testThatPropExpectationsCanTestCombinedTypes()
    {
        // ARRANGE
        $bag = new BrickPropsBag([
            'qty_ordered' => 5.5,
            'qty_shipped' => 4
        ]);

        // EXPECT
        $this->expectNotToPerformAssertions();

        // ACT
        $bag->expect(['qty_ordered' => 'int|float', 'qty_shipped' => 'int|float']);
    }

    public function testThatPropExpectationsRejectInvalidNullableDefinitions()
    {
        // ARRANGE
        $bag = new BrickPropsBag([
            'qty_ordered' => 5.5,
            'qty_shipped' => 4
        ]);

        // EXPECT
        $this->expectException(PropExpectedTypeStringInvalidException::class);
        $this->expectExceptionMessage("Cannot use '?' to start a prop's type-string AND use |; use |null instead.");

        // ACT
        $bag->expect(['qty_ordered' => '?int|float', 'qty_shipped' => 'int|float']);
    }
}
