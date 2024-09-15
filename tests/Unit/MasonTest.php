<?php

namespace Corrivate\RestApiLogger\Tests\Unit;

use Corrivate\LayoutBricks\Model\Mason;
use Magento\Framework\View\Layout;
use PHPUnit\Framework\TestCase;

class MasonTest extends TestCase
{
    public function testThatTemplateAliasesCanBeUsedToReferenceTemplates()
    {
        // ARRANGE
        $layout = $this->createMock(Layout::class);
        $block = $this->createMock(\Magento\Framework\View\Element\Template::class);

        $layout->expects($this->once())->method('createBlock')->with(\Magento\Framework\View\Element\Template::class)->willReturn($block);
        $block->expects($this->once())->method('setTemplate')->with('Corrivate_LayoutBricks::elements/link/external.phtml')->willReturn($block);
        $block->expects($this->atLeastOnce())->method('setData')->willReturn($block);

        /** @var Layout $layout */
        $mason = new Mason(
            $layout,
            ['elements.link.external' => 'Corrivate_LayoutBricks::elements/link/external.phtml']
        );

        // ACT
        try {
            $mason('elements.link.external');
        } catch (\TypeError $e) {
            // Type error is to be expected, because we didn't give $mason a real Layout or Block to work with.
        }

        // ASSERT
        // Our mocks have received expected method calls
    }


    public function testThatItThrowsWhenGivenAnUnknownAlias(){
        // ARRANGE
        $layout = $this->createMock(Layout::class);


        /** @var Layout $layout */
        $mason = new Mason(
            $layout,
            [] // No aliases configured
        );

        $this->expectException(\InvalidArgumentException::class);

        // ACT
        $mason('elements.link.external');

        // ASSERT
        // The exception should have been thrown
    }


    public function testItDoesNotTreatAMagentoPathAsAlias(){
        // ARRANGE
        $layout = $this->createMock(Layout::class);
        $block = $this->createMock(\Magento\Framework\View\Element\Template::class);

        $layout->expects($this->once())->method('createBlock')->with(\Magento\Framework\View\Element\Template::class)->willReturn($block);
        $block->expects($this->once())->method('setTemplate')->with('Corrivate_LayoutBricks::elements/link/external.phtml')->willReturn($block);
        $block->expects($this->atLeastOnce())->method('setData')->willReturn($block);
        $block->expects($this->atLeastOnce())->method('toHtml')->willReturn('html');


        /** @var Layout $layout */
        $mason = new Mason(
            $layout,
            [] // No aliases configured
        );

        // ACT
        $mason('Corrivate_LayoutBricks::elements/link/external.phtml');

        // ASSERT
        // The exception should have been thrown
    }

}
