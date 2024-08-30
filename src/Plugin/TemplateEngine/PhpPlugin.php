<?php

namespace Corrivate\LayoutBricks\Plugin\TemplateEngine;

use Corrivate\LayoutBricks\Model\BrickLayer;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngine\Php;

class PhpPlugin
{
    public function __construct(
        private readonly BrickLayer $brick
    ){}
    /**
     * @param  Php  $subject
     * @param  BlockInterface  $block
     * @param  string  $fileName
     * @param  array  $dictionary
     * @return array
     */
    public function beforeRender(Php $subject, BlockInterface $block, $fileName, array $dictionary = []): array
    {
        $dictionary['brick'] = $this->brick;
        return [$block, $fileName, $dictionary];
    }
}
