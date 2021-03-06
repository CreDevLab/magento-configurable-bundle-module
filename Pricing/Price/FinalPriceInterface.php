<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Credevlab\Composite\Pricing\Price;

/**
 * Interface FinalPriceInterface
 * @api
 * @since 100.0.2
 */
interface FinalPriceInterface extends \Magento\Catalog\Pricing\Price\FinalPriceInterface
{
    /**
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getPriceWithoutOption();
}
