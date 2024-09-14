<?php

declare(strict_types=1);

namespace Corrivate\RestApiLogger\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Corrivate\LayoutBricks\Model\BrickAttributesBag;

class BrickAttributesBagTest extends TestCase
{
    public function testThatAnEmptyBagCanBeRendered(){
        // ARRANGE
        $bag = new BrickAttributesBag();

        // ACT
        $output = $bag->toHtml();

        // ASSERT
        $this->assertSame(0, count($bag));
        $this->assertSame('', $output);
    }
}
