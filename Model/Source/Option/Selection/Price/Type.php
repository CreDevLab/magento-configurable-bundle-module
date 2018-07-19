<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\ConfigurableBundle\Model\Source\Option\Selection\Price;

use Credevlab\ConfigurableBundle\Api\Data\LinkInterface;

/**
 * Extended Attributes Source Model
 *
 * @api
 * @since 100.0.2
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => LinkInterface::PRICE_TYPE_FIXED, 'label' => __('Fixed')],
            ['value' => LinkInterface::PRICE_TYPE_PERCENT, 'label' => __('Percent')]
        ];
    }
}
