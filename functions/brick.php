<?php

namespace Corrivate\LayoutBricks;

use Corrivate\LayoutBricks\Model\Alias;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;

function brick(
    string $template = '',
    array $with = [],
    string $block = Template::class
): string
{
    $objectManager = ObjectManager::getInstance();

    if(!preg_match('/phtml$/', $template)) {
        $aliasModel = $objectManager->get(Alias::class);
        $template = $aliasModel->decode($template);
    }

    return $objectManager
        ->get(Layout::class)
        ->createBlock($block)
        ->setTemplate($template)
        ->setData('with', $with)
        ->toHtml();
}
