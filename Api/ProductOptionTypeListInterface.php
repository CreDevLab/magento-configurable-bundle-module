<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\Composite\Api;

/**
 * Interface ProductOptionTypeListInterface
 * @api
 * @since 100.0.2
 */
interface ProductOptionTypeListInterface
{
    /**
     * Get all types for options for bundle products
     *
     * @return \Credevlab\Composite\Api\Data\OptionTypeInterface[]
     */
    public function getItems();
}
