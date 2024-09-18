# Layout Bricks for Magento


[![Latest Version on Packagist](https://img.shields.io/packagist/v/corrivate/magento2-layout-bricks?color=blue)](https://packagist.org/packages/corrivate/magento2-layout-bricks)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

*All in all you're just another brick in the layout*

```bash
composer require corrivate/magento2-layout-bricks
```

Modern frontend frameworks embrace reusable components, like buttons, input fields, and cards. And they style them with utility CSS like Tailwind. It's fine to pile a dozen classes on that primary button, because you only have to build it once.

Magento doesn't come with this out of the box. Many templates are *huge* and if you talk about UI components people make the sign of the ~~cross~~ XML at you. 

This package is a way to make things better. To use small anonymous components without hassle. It's heavily inspired by Laravel's anonymous blade components. In Magento, our unit of frontend template is a block. An anonymous block is a **brick**.

## An example phtml template
```php
<?php declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\Mason $mason */
?>

<form method="post" action="/newsletter/subscribe">
<?= $mason('cms.block', props: ['block_id' => 'newsletter-explanation']) ?>

<?= $mason('ExampleCorp_ExampleModule::theme/input/text.phtml', [
    'required', 
    'class' => 'rounded-md text-stone-800 bg-stone-100', 
    'placeholder' => 'joe@examplecorp.com'
]) ?>

<?= $mason('btn-primary', attributes: ['type' => 'submit'], props: ['label' => __('Save')]) ?>
</form>
```

## How does it work?

The `$mason` object is globally injected into every `.phtml` template. It has just one method, `__invoke()`, to cause it to output as a string a fully rendered child block. So it's essentially a compact, ergonomic way of doing this:

```php
<?= $block
    ->getLayout()
    ->createBlock(\Magento\Framework\View\Element\Template::class)
    ->setTemplate($template) 
?>
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
<?= $mason('cms.block', 
        attributes: ['class' => 'border-2 border-stone-400 rounded-lg'], 
        props: ['block_id' => 'text-block']) 
?>
```

This will render the CMS block with ID 'text-block', but surround it in a div with a gray round border.

## Aliases

[Aliases in detail](docs/Aliases.md)

You can place bricks in two ways:
* Fully cite the Magento template path:

```php
<?= $mason('Corrivate_LayoutBricks::cms/block.phtml', props: ['block_id' => 'test-block']) ?>
``` 

* Create an **alias** for it, so you can refer to it more shortly: 

```php
<?= $mason('cms.block', props: ['block_id' => 'test-block']) ?>
``` 

[Aliases in detail](docs/Aliases.md)

## Attributes

[Attributes in detail](docs/Attributes.md)

The `$mason` objects invoke method accepts an array of attributes. For example:

```php
<?= $mason('input.text', attributes: [
    'required', 
    'disabled' => false, 
    'class' => 'text-black', 
    'placeholder' => 'your input please', 
    'name' => 'user_comment'
]) ?>
```

In the brick template, this will be available as a BrickAttributesBag which could for example have the following default attributes/values:

```php
<input <?= $attributes->default([
    'class' => 
    'bg-white', 
    'disabled' => true, 
    'type' => 'text'
]) ?> />
```

This would result in the following HTML after the defaults and your custom input is merged:

```html
<input class="bg-white text-black" 
       type="text" 
       required 
       placeholder="your input please" 
       name="user_comment"/>
```

[Attributes in detail](docs/Attributes.md)

## Props

[Props in detail](docs/Props.md)

Props are used to pass data to the brick. For example, if you were making a brick to render a "product card", you'd pass the product that needs to be displayed:

```php
<?= $mason('product-cart', props: ['product' => $product]) ?>
```

In the brick template, the props are available through the `$props` variable, which is automatically present:

```php
<?php declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\BrickAttributesBag $attributes */
/** @var \Corrivate\LayoutBricks\Model\BrickPropsBag $props */
?>

<div class="border-2 border-color-stone-600 rounded-md">
    <?= $props['product']->getSku() ?>
</div>

```

The `$props` variable is not an array, but it implements `ArrayAccess` to give access to its contents.

The `$props` variable also has a `$props->default([])` method so you can supply default (scalar) props. You can always override those default from the parent template. 

The `$props` object also has a `$props->expect([])` method which allows you to specify expected props and their data types so you can opt into greater type safety.

[Props in detail](docs/Props.md)


## Escaper
Using `$escaper` to filter raw output of data from the DB or user input is important to protect against various attacks. [Official documentation](https://developer.adobe.com/commerce/php/development/security/cross-site-scripting/#phtml-templates) about this. 

However, the output from `$mason()` is the output of another block that already produces HTML. So the call to `$mason()` should NOT be escaped, but inside the PHTML template implementing your brick, you should use it normally.

## Empty PHTML brick template

Just copy paste this into a file and start designing:

```php
<?php declare(strict_types=1);
/** @var \Magento\Framework\View\Element\Template $block */
/** @var \Magento\Framework\Escaper $escaper */
/** @var \Corrivate\LayoutBricks\Model\BrickAttributesBag $attributes */
/** @var \Corrivate\LayoutBricks\Model\BrickPropsBag $props */

// For Hyva users:
/** @var \Hyva\Theme\Model\ViewModelRegistry $viewModels */

$props->default([
    // you can supply default scalar values for your props
])->expect([
    // specify propName => type for your props
]);
?>


<div <?= $attributes->default(['class' => '']) ?>>

</div>

```


## Corrivate
(en.wiktionary.org)

Etymology

From Latin *corrivatus*, past participle of *corrivare* ("to corrivate").

### Verb

**corrivate** (*third-person singular simple present* **corrivates**, *present participle* **corrivating**, *simple past and past participle* **corrivated**)

(*obsolete*) To cause to flow together, as water drawn from several streams. 
