<?php

declare(strict_types=1);

namespace Corrivate\RestApiLogger\Tests\Unit;

use Corrivate\LayoutBricks\Model\BrickPropsBag;
use PHPUnit\Framework\TestCase;

class BrickPropsBagTest extends TestCase
{
    public function testThatEmptyPropsBagCanBeInstantiated(){
        // ARRANGE
        $bag = new BrickPropsBag([]);

        // ACT
        // nothing to do here

        // ASSERT
        $this->assertSame(0, count($bag));

    }
}
