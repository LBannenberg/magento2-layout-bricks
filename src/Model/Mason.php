<?php

namespace Corrivate\LayoutBricks\Model;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;

readonly class Mason
{
    /**
     * @param  array<string, string>  $aliases
     */
    public function __construct(
        private Layout $layout,
        public array $aliases = [
            // 'alias' => 'Vendor_Module::path/to/template.phtml' ; inject through frontend/di.xml or adminhtml/di.xml
        ]
    ){}


    public function __invoke(
        string $template = '',
        array $with = [],
        array $attributes = [],
        array $props = [],
        string $block = Template::class
    ): string
    {
        if(!preg_match('/phtml$/', $template)) {
            $template = $this->decodeAlias($template);
        }

        return $this->layout
            ->createBlock($block)
            ->setTemplate($template)
            ->setData('with', $with)
            ->setData('is_brick', true)
            ->setData('brick_attributes', $attributes)
            ->toHtml();
    }


    public function decodeAlias(string $alias): string
    {
        if(isset($this->aliases[$alias])) {
            return $this->aliases[$alias];
        }
        throw new \InvalidArgumentException("BrickLayer alias [ $alias ] not configured.");
    }
}