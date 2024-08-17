<?php

namespace Corrivate\LayoutBricks\Model;

class Alias
{
    public function decode(string $alias): string
    {
        return 'ExampleCorp_Demo::button-primary.phtml';
    }
}
