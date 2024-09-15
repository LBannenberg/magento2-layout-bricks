<?php

declare(strict_types=1);

namespace Corrivate\RestApiLogger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Corrivate\LayoutBricks\Model\BrickAttributesBag;

class BrickAttributesBagTest extends TestCase
{
    public function testThatAnEmptyBagCanBeRendered()
    {
        // ARRANGE
        $bag = new BrickAttributesBag();

        // ACT
        $output = $bag->toHtml();

        // ASSERT
        $this->assertSame(0, count($bag));
        $this->assertSame('', $output);
    }


    public function testThatInitialAttributesArePrintedSeparatedBySpaces()
    {
        // ARRANGE
        $bag = new BrickAttributesBag(['class' => 'bg-black', 'foo' => 'bar']);

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame(2, count($bag));
        $this->assertSame('class="bg-black" foo="bar"', $bag->toHtml());
    }


    public function testThatDefaultAttributesCanBeSet()
    {
        // ARRANGE
        $bag = new BrickAttributesBag();

        // ACT
        $bag->merge(['foo' => 'bar']);

        // ASSERT
        $this->assertSame(1, count($bag));
        $this->assertSame('foo="bar"', $bag->toHtml());
    }


    public function testThatDefaultClassesArePrependedBeforeInjectedClasses()
    {
        // ARRANGE
        $bag = new BrickAttributesBag(['class' => 'bg-black']);

        // ACT
        $bag->merge(['class' => 'text-white']);

        // ASSERT
        $this->assertSame(1, count($bag));
        $this->assertSame('class="text-white bg-black"', $bag->toHtml());
    }


    public function testThatBooleanAttributesAreRenderedOnlyIfTruthy()
    {
        // ARRANGE
        $bag = new BrickAttributesBag([
            'required',
            'checked' => true,
            'autoplay',
            'disabled' => false,
            'selected' => 'false' // haha! string is not falsey!
        ]);

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame('required checked autoplay selected', $bag->toHtml());
    }


    public function testThatInjectedBooleanAttributesTrumpDefaultValues()
    {
        // ARRANGE
        $bag = new BrickAttributesBag([
            'checked' => true,
            'autoplay',
            'disabled' => false,
            'selected' => 'false' // haha! string is not falsey!
        ]);

        // ACT
        $bag->merge(['required', 'checked' => false, 'disabled' => true]);

        // ASSERT
        $this->assertSame('required checked autoplay selected', $bag->toHtml());
    }


    public function testThatInjectedStylesAreAppendedAfterDefaults(){
        // ARRANGE
        $bag = new BrickAttributesBag([
            'style' => 'height:12px' // intentionally forgetting closing ';'
        ]);

        // ACT
        $bag->merge(['style' => 'width:12px;']);

        // ASSERT
        $this->assertSame('style="width:12px; height:12px;"', $bag->toHtml());
    }


    public function testThatWhereStartsWithReturnsANewBagWithOnlyThoseAttributes(){
        // ARRANGE
        $bag = new BrickAttributesBag([
            'required',
            'disabled',
            'checked',
            'selected',
            'readonly'
        ]);

        // ACT
        $only = $bag->whereStartsWith('re');

        // ASSERT
        $this->assertSame('required disabled checked selected readonly', $bag->toHtml());
        $this->assertSame('required readonly', $only->toHtml());
    }


    public function testThatWhereDoesntStartWithReturnsANewBagWithoutThoseAttributes(){
        // ARRANGE
        $bag = new BrickAttributesBag([
            'required',
            'disabled',
            'checked',
            'selected',
            'readonly'
        ]);

        // ACT
        $without = $bag->whereDoesntStartWith('re');

        // ASSERT
        $this->assertSame('required disabled checked selected readonly', $bag->toHtml());
        $this->assertSame('disabled checked selected', $without->toHtml());
    }


    public function testThatOnlyReturnsABagWithOnlyThoseAttributes(){
        // ARRANGE
        $bag = new BrickAttributesBag([
            'required',
            'disabled',
            'checked',
            'selected',
            'readonly'
        ]);

        // ACT
        $without = $bag->only('disabled', 'checked', 'selected');

        // ASSERT
        $this->assertSame('required disabled checked selected readonly', $bag->toHtml());
        $this->assertSame('disabled checked selected', $without->toHtml());
    }


    public function testThatWithoutReturnsABagWithoutThoseAttributes(){
        // ARRANGE
        $bag = new BrickAttributesBag([
            'required',
            'disabled',
            'checked',
            'selected',
            'readonly'
        ]);

        // ACT
        $without = $bag->without('disabled', 'checked', 'selected');

        // ASSERT
        $this->assertSame('required disabled checked selected readonly', $bag->toHtml());
        $this->assertSame('required readonly', $without->toHtml());
    }
}
