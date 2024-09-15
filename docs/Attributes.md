# Attributes

## Merging default and injected attributes

The module provides a (Laravel-inspired) way to combine default and customized HTML attributes. From the outside, this looks like this:

```PHP
<?php ?>
<div>
<?= $mason('btn-primary', ['class' => 'text-bold text-blue-800']) ?>
</div>
```

And inside the component it would look like:

```PHP
<?php
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\BrickAttributesBag $attributes */
/** @var \Corrivate\LayoutBricks\Model\BrickPropsBag $props */
?>

<button <?= $attributes->merge(['type' => 'button', 'class' => 'text-black bg-green-500']) ?>>
    <?= __('Ok') ?>
</button>
```

The resulting HTML would be:

```html
<div>
    <button type="button" class="text-black bg-green-500 text-bold text-blue-800">Ok</button>
</div>
```

As you can see, we get both the default attributes (type=button, text-black) as the injected ones (text-blue-800). Because the injected CSS classes are placed last, and assuming no weird specificity problems, they will have the last word.

**NOTE** The `merge()` method is in-place, so if you call:

```php
$attributes->merge(['style' => 'display: none;']);
echo $attributes;
```

You'll get a `style="display: none;"`, because the `$attributes` object has been modified.

## Accessing a specific attribute

The attributes are supplied by the BrickAttributesBag class, which is a data carrier that presents an ArrayAccess interface so you can also for example reach into it to grab a specific attribute:

```php
$attributes['style'] = 'display: none;'
echo $attributes['style'];
```

## Using `only` or `without` some attributes

If you need a handful of attributes which may or may not have been set, you can use the `only` method (and it's counterpart, the `without` method):

```php
echo $attributes->only('required', 'disabled', 'readonly');
echo $attributes->without('required', 'disabled', 'readonly');
```

These methods return a **new copy** of the `$attributes` with only the requested keys.

## Using `whereStartsWith` and `WhereDoesntStartWith`

When working with Magewire and AlpineJS it may also be useful to filter attributes based on prefix:

```php
<?php ?>

<div <?= $attributes->whereStartsWith('wire:') ?>>
...
</div>

<div <?= $attributes->whereDoesntStartWith('x-') ?> >
...
</div>
```

These methods return a **new copy** of the `$attributes` with only the requested keys.

## Boolean HTML attributes

Attributes like `required` are Boolean: either they're present on a HTML element and true, or they're completely absent. But we often want to set them based on conditions. We can do this:

```php
<?= $mason('btn-primary', ['disabled' => $session->isLoggedIn()]) ?>
```

Depending on whether `isLoggedIn()` turns out to be true, we would get either of these:

```html
<button class="text-black bg-green-500" disabled>OK</button>
<button class="text-black bg-green-500" >OK</button>
```

This follows the standard that Boolean HTML attributes should be simply present when true and absent when false.

Note that it's also possible to just pass in straight strings, if you don't need extra logic:

```php
<?= $mason('btn-primary', ['disabled']) ?>
```
Will result in:
```html
<button class="text-black bg-green-500" disabled>OK</button>
```
