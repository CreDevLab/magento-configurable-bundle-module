<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\Composite\Pricing\Price;

/**
 * Option price interface
 * @api
 * @since 100.0.2
 */
interface BundleOptionPriceInterface
{
    /**
     * Return calculated options
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param \Credevlab\Composite\Model\Selection $selection
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getOptionSelectionAmount($selection);
}
