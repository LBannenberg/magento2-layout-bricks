# Layout Bricks for Magento

*All in all you're just another brick in the layout*

Modern frontend frameworks embrace reusable components, like buttons, input fields, and cards. And they style them with utility CSS like Tailwind. It's fine to pile a dozen classes on that primary button, because you only have to build it once.

Magento doesn't come with this out of the box. Many templates are *huge* and if you talk about UI components people make the sign of the ~~cross~~ XML at you. 

This package is a way to make things better. To use small anonymous components without hassle. It's heavily inspired by Laravel's anonymous blade components. In Magento, our unit of frontend template is a block. An anonymous block is a **brick**.

## An example phtml template
```php
<?php
declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\Mason $mason */
?>

<form method="post" action="/newsletter/subscribe">
<?= $mason('cms.block', props: ['block_id' => 'newsletter-explanation']) ?>

<?= $mason('ExampleCorp_ExampleModule::theme/input/text.phtml', ['required', 'class' => 'rounded-md text-stone-800 bg-stone-100', 'placeholder' => 'joe@examplecorp.com']) ?>

<?= $mason('btn-primary', attributes: ['type' => 'submit'], props: ['label' => __('Save')]) ?>
</form>
```

## How does it work?

The `$mason` object is globally injected into every `.phtml` template. It has just one method, `__invoke()`, to cause it to output as a string a fully rendered child block. So it's essentially a compact, ergonomic way of doing this:

```php
<?= $block->layout->createBlock(\Magento\Framework\View\Element\Template::class)->setTemplate($template) ?>
```

This is already nice, because we are now still using Magento's templating engine:
* We can call small templates without using a ton of boilerplate. It's now realistic to make a template for something as small as a single button. So we can re-use the same button look and feel throughout the entire website. This is really helpful if the button actually has a LOT of utility CSS classes. Hi Tailwind.
* We can make a library of base components as a reusable module. 
* We still have the opportunity to use Magento's theme overrides. We can change the way buttons look in a website or single store. But because we're re-using the button template everywhere, we can change it in one place and have the change happen everywhere.

But there's more:

* You can set default HTML attributes (such as classes) on a component, and inject additional ones based on the context. They will be merged, with new properties overriding default ones.
* You can inject props into a component, supplying them with data.

For example, consider the `cms.block` brick: 
```php
<?= $mason('cms.brick', attributes: ['class' => 'border-2 border-stone-400 rounded-lg'], props: ['block_id' => 'text-block']) ?>
```

This will render the CMS block with ID 'text-block', but surround it in a div with a gray round border.

## Aliases

You can place bricks in two ways:
* Directly cite the Magento template path:

```php
<?= $mason('Corrivate_LayoutBricks::cms/block.phtml', props: ['block_id' => 'test-block']) ?>
``` 

* Create an **alias** for it, so you can refer to it more shortly: 

```php
<?= $mason('cms.block', props: ['block_id' => 'test-block']) ?>
``` 

Aliases are created by injecting the with `frontend/di.xml` into the `\Corrivate\LayoutBricks\Model\Mason` constructor's `aliases` array:

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Corrivate\LayoutBricks\Model\Mason">
        <arguments>
            <argument name="aliases" xsi:type="array">
                <item name="cms.block" xsi:type="string">Corrivate_LayoutBricks::cms/block.phtml</item>
            </argument>
        </arguments>
    </type>
</config>
```

## Attributes

The `$mason` objects invoke method accepts an array of attributes. For example:

```php
<?= $mason('input.text', ['required', 'disabled' => false, 'class' => 'text-black', 'placeholder' => 'your input please', 'name' => 'user_comment']) ?>
```

In the brick template, this will be available as a BrickAttributesBag which could for example have the following default attributes/values:

```php
<input <?= $attributes->merge(['class' => 'bg-white', 'disabled' => true, 'type' => 'text']) ?> />
```

This would result in the following HTML:

```html
<input class="bg-white text-black" type="text" required placeholder="your input please" name="user_comment"/>
```

* For most html attributes, the default value is printed unless there's a different value injected, then the injected value is printed.
* For boolean html attributes (like 'required'), they're only printed if they are present with no value in the array, or truthy. Again, you can inject a value to override the default.
* For style and class attributes, the injected values are appended after the default. With for example Tailwind CSS, this allows them to override the defaults because they come last.
* Merging is in-place.

## Props

Props are used to pass data to the brick. For example, if you were making a brick to render a "product card", you'd pass the product that needs to be displayed:

```php
<?= $mason('product-cart', props: ['product' => $product]) ?>
```

In the brick template, the props are available through the `$props` variable, which is automatically present:

```php
<?php
declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\BrickAttributesBag $attributes */
/** @var \Corrivate\LayoutBricks\Model\BrickPropsBag $props */
?>

<?= $props['product']->getSku() ?>
```

The `$props` variable is not an array, but it implements `ArrayAccess` to give access to its contents.

The `$props` variable also has a `$props->merge([])` method so you can supply default props which can you can then override from the parent template. Merging is in-place.


## Escaper
Because your bricks are almost certainly going to use HTML and might also involve some more fancy stuff (like entire Alpine components or reused JS functions) you cannot say that in general you should use this or that `$escaper` method on bricks that you insert.

Rather, inside those brick templates themselves you should consider which parts should be escaped. When designing a brick template, you should ensure that the end user of the brick doesn't have to worry about it.
