<?php

declare(strict_types=1);

namespace Corrivate\RestApiLogger\Tests\Unit;

use Corrivate\LayoutBricks\Model\BrickPropsBag;
use PHPUnit\Framework\TestCase;

class BrickPropsBagTest extends TestCase
{
    public function testThatEmptyPropsBagCanBeInstantiated(){
        // ARRANGE
        $bag = new BrickPropsBag();

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame(0, count($bag));
    }

    public function testThatInitialPropsCanBeAccessed(){
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'bar']);

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame('bar', $bag['foo']);
    }

    public function testThatDefaultPropsCanBeMergedAndAccessed(){
        // ARRANGE
        $bag = new BrickPropsBag();

        // ACT
        $bag->default(['foo' => 'bar']);

        // ASSERT
        $this->assertSame('bar', $bag['foo']);
    }

    public function testThatInjectedPropsReplaceDefaultProps(){
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'baz']);

        // ACT
        $bag->default(['foo' => 'bar']);

        // ASSERT
        $this->assertSame('baz', $bag['foo']);
    }


    public function testThatBothDefaultAndInjectedPropsCanBeUsedTogether(){
        // ARRANGE
        $bag = new BrickPropsBag(['foo' => 'baz']);

        // ACT
        $bag->default(['bar' => 'bar']);

        // ASSERT
        $this->assertSame('baz', $bag['foo']);
        $this->assertSame('bar', $bag['bar']);
    }
}
