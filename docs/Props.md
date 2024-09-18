# Props

Props are used to pass data to the brick component. In the template, it could look something like this:

```php
<?php declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\BrickAttributesBag $attributes */
/** @var \Corrivate\LayoutBricks\Model\BrickPropsBag $props */

$props->default([ 'title' => 'My Product Card' ])
    ->expect([
        'title' => 'string', 
        'special_price' => '?float',
        'initial_quantity' => 'int|float|null'
        'product' => \Magento\Catalog\Api\Data\ProductInterface::class
    ]);

$product = $props['product'];
?>

<div <?= $attributes->default(['class' => 'border-2 border-color-stone-600 rounded-md']) ?>>
    <h3><?= $props['title'] ?></h3>
    <p><?= $product->getSku() ?></p>
    <p><?= $product->getPrice() ?></p>
    <input type="number" name="qty" value"<?= $props['initial_quantity'] ?>" step="0.1"/>
</div>
```

The `$props` object has two public methods:
* With `default()` you can specify default (scalar) values for props. If the parent template that's using this brick injects its own values, those will override the default values. 
  * You can supply scalar values here and a few adjacent ones like Phrase. 
  * It would be a bad practice to start DI-ing more complex default types here, because then you're starting to put a lot of logic in your view templates.
* With `expect()` you can specify which props you're expecting, and if they're mandatory. 
  * A prop expectation starting with '?' is optional/nullable. If it's not supplied at all, it will be set with a `$propName = null` assignment. If it's present, it will be validated against expectations.
  * If you allow multiple types, such as `int|float|null` you cannot use the `?` prefix syntax (consistent with regular PHP function signatures).
  * You can specify interfaces and parent types, and concrete & child types will be accepted as well (we use `instanceof` to check). For example, if you're expecting a ProductInterface, you can supply a concrete Product.
