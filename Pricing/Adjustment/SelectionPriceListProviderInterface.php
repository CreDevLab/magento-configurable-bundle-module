<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Credevlab\Composite\Pricing\Adjustment;

use Magento\Catalog\Model\Product;

/**
 * Provide list of bundle selection prices
 * @api
 * @since 100.2.0
 */
interface SelectionPriceListProviderInterface
{
    /**
     * @param Product $bundleProduct
     * @param boolean $searchMin
     * @param boolean $useRegularPrice
     * @return \Credevlab\Composite\Pricing\Price\BundleSelectionPrice[]
     * @since 100.2.0
     */
    public function getPriceList(Product $bundleProduct, $searchMin, $useRegularPrice);
}
