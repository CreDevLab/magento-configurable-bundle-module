<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Credevlab\ConfigurableBundle\Pricing\Price;

/**
 * Interface DiscountProviderInterface
 * @api
 * @since 100.0.2
 */
interface DiscountProviderInterface
{
    /**
     * @return float
     */
    public function getDiscountPercent();
}
