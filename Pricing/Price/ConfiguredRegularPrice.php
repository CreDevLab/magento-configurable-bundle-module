<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Credevlab\ConfigurableBundle\Pricing\Price;

/**
 * Configured regular price model
 */
class ConfiguredRegularPrice extends ConfiguredPrice
{
    /**
     * Price type configured
     */
    const PRICE_CODE = 'configured_regular_price';

    /**
     * Create Selection Price List
     *
     * @param \Credevlab\ConfigurableBundle\Model\Option $option
     * @return BundleSelectionPrice[]
     */
    protected function createSelectionPriceList(\Credevlab\ConfigurableBundle\Model\Option $option): array
    {
        return $this->calculator->createSelectionPriceList($option, $this->product, true);
    }
}
