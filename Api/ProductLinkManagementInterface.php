<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\ConfigurableBundle\Api;

/**
 * Interface for Management of ProductLink
 * @api
 * @since 100.0.2
 */
interface ProductLinkManagementInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param string $productSku
     * @param int $optionId
     * @return \Credevlab\ConfigurableBundle\Api\Data\LinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getChildren($productSku, $optionId = null);

    /**
     * Add child product to specified Bundle option by product sku
     *
     * @param string $sku
     * @param int $optionId
     * @param \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChildByProductSku($sku, $optionId, \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct);

    /**
     * @param string $sku
     * @param \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function saveChild(
        $sku,
        \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct
    );

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $optionId
     * @param \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChild(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $optionId,
        \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct
    );

    /**
     * Remove product from Bundle product option
     *
     * @param string $sku
     * @param int $optionId
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function removeChild($sku, $optionId, $childSku);
}