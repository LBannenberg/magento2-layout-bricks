# Aliases

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

It's important to target the `frontend/di.xml` (or `adminhtml/di.xml`) files, not the base `etc/di.xml` file, because the frontend injected dependencies will cause any deps you try to inject in the base area to be ignored.
