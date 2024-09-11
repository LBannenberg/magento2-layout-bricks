<?php

namespace Corrivate\LayoutBricks\Model;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;

class Mason
{
    private array $aliases;
    private Layout $layout;

    /**
     * @param  array<string, string>  $aliases
     */
    public function __construct(
        Layout $layout,
        array $aliases = [
            // 'alias' => 'Vendor_Module::path/to/template.phtml' ; inject through frontend/di.xml or adminhtml/di.xml
        ]
    ){
        $this->aliases = $aliases;
        $this->layout = $layout;
    }


    public function __invoke(
        string $template = '', // Alias or Magento path
        array $attributes = [],
        array $props = [],
        string $block = Template::class
    ): string
    {
        // Is it a Vendor_Module::path/to/template.phtml Magento path?
        if(!preg_match('/^\w+_\w+::[\w\/]+\.phtml$/', $template)) {
            $template = $this->decodeAlias($template);
        }

        return $this->layout
            ->createBlock($block)
            ->setTemplate($template)
            ->setData('is_brick', true)
            ->setData('brick_attributes', $attributes)
            ->setData('brick_props', $props)
            ->toHtml();
    }


    private function decodeAlias(string $alias): string
    {
        if(isset($this->aliases[$alias])) {
            return $this->aliases[$alias];
        }
        throw new \InvalidArgumentException("\$mason alias [ $alias ] not configured.");
    }
}
