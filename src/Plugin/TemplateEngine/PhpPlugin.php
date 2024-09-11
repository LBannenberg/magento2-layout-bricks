<?php

namespace Corrivate\LayoutBricks\Plugin\TemplateEngine;

use Corrivate\LayoutBricks\Model\BrickAttributesBagFactory;
use Corrivate\LayoutBricks\Model\BrickPropsBagFactory;
use Corrivate\LayoutBricks\Model\Mason;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngine\Php;

class PhpPlugin
{
    private Mason $mason;
    private BrickAttributesBagFactory $brickAttributesBagFactory;
    private BrickPropsBagFactory $brickPropsBagFactory;

    public function __construct(
        Mason $mason,
        BrickAttributesBagFactory $brickAttributesBagFactory,
        BrickPropsBagFactory $brickPropsBagFactory
    ) {
        $this->brickAttributesBagFactory = $brickAttributesBagFactory;
        $this->mason = $mason;
        $this->brickPropsBagFactory = $brickPropsBagFactory;
    }

    /**
     * @param  Php  $subject
     * @param  BlockInterface  $block
     * @param  string  $fileName
     * @param  array  $dictionary
     * @return array
     */
    public function beforeRender(Php $subject, BlockInterface $block, $fileName, array $dictionary = []): array
    {
        $dictionary['mason'] = $this->mason;

        if ($block->getData('is_brick')) {
            $dictionary['attributes'] = $this->brickAttributesBagFactory->create(
                ['attributes' => $block->getData('brick_attributes')]
            );
            $dictionary['props'] = $this->brickPropsBagFactory->create(
                ['props' => $block->getData('brick_props')]
            );
            $dictionary['with'] = $block->getData('with');
        }

        return [$block, $fileName, $dictionary];
    }
}
