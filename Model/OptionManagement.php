<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\Composite\Model;

use Magento\Framework\Exception\InputException;
use Credevlab\Composite\Model\Product\Type;

class OptionManagement implements \Credevlab\Composite\Api\ProductOptionManagementInterface
{
    /**
     * @var \Credevlab\Composite\Api\ProductOptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Credevlab\Composite\Api\ProductOptionRepositoryInterface $optionRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Credevlab\Composite\Api\ProductOptionRepositoryInterface $optionRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->optionRepository = $optionRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Credevlab\Composite\Api\Data\OptionInterface $option)
    {
        $product = $this->productRepository->get($option->getSku(), true);
        if ($product->getTypeId() != Type::TYPE_CODE) {
            throw new InputException(__('Only implemented for configurable bundle product'));
        }
        return $this->optionRepository->save($product, $option);
    }
}
