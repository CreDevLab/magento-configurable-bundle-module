<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\ConfigurableBundle\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product;
use Credevlab\ConfigurableBundle\Model\Product\Type;

class ConfigurableBundle implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * Duplicating bundle options and selections
     *
     * @param Product $product
     * @param Product $duplicate
     * @return void
     */
    public function build(Product $product, Product $duplicate)
    {
        if ($product->getTypeId() != Type::TYPE_CODE) {
            //do nothing if not configurable bundle
            return;
        }

        $bundleOptions = $product->getExtensionAttributes()->getBundleProductOptions() ?: [];
        $duplicatedBundleOptions = [];
        foreach ($bundleOptions as $key => $bundleOption) {
            $duplicatedBundleOption = clone $bundleOption;
            /**
             * Set option and selection ids to 'null' in order to create new option(selection) for duplicated product,
             * but not modifying existing one, which led to lost of option(selection) in original product.
             */
            $productLinks = $duplicatedBundleOption->getProductLinks() ?: [];
            foreach ($productLinks as $productLink) {
                $productLink->setSelectionId(null);
            }
            $duplicatedBundleOption->setOptionId(null);
            $duplicatedBundleOptions[$key] = $duplicatedBundleOption;
        }
        $duplicate->getExtensionAttributes()->setBundleProductOptions($duplicatedBundleOptions);
    }
}
