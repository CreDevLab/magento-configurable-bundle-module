<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\Composite\Api;

/**
 * Option manager for bundle products
 *
 * @api
 * @since 100.0.2
 */
interface ProductOptionManagementInterface
{
    /**
     * Add new option for bundle product
     *
     * @param \Credevlab\Composite\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save(\Credevlab\Composite\Api\Data\OptionInterface $option);
}
