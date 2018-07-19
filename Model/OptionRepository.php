<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\ConfigurableBundle\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Credevlab\ConfigurableBundle\Model\Product\Type;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OptionRepository implements \Credevlab\ConfigurableBundle\Api\ProductOptionRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Product\Type
     */
    protected $type;

    /**
     * @var \Credevlab\ConfigurableBundle\Api\Data\OptionInterfaceFactory
     */
    protected $optionFactory;

    /**
     * @var \Magento\Bundle\Model\ResourceModel\Option
     */
    protected $optionResource;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Credevlab\ConfigurableBundle\Api\ProductLinkManagementInterface
     */
    protected $linkManagement;

    /**
     * @var Product\OptionList
     */
    protected $productOptionList;

    /**
     * @var Product\LinksList
     */
    protected $linkList;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param Product\Type $type
     * @param \Credevlab\ConfigurableBundle\Api\Data\OptionInterfaceFactory $optionFactory
     * @param \Magento\Bundle\Model\ResourceModel\Option $optionResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Credevlab\ConfigurableBundle\Api\ProductLinkManagementInterface $linkManagement
     * @param Product\OptionList $productOptionList
     * @param Product\LinksList $linkList
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Type $type,
        \Credevlab\ConfigurableBundle\Api\Data\OptionInterfaceFactory $optionFactory,
        \Magento\Bundle\Model\ResourceModel\Option $optionResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Credevlab\ConfigurableBundle\Api\ProductLinkManagementInterface $linkManagement,
        \Magento\Bundle\Model\Product\OptionList $productOptionList,
        \Magento\Bundle\Model\Product\LinksList $linkList,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->productRepository = $productRepository;
        $this->type = $type;
        $this->optionFactory = $optionFactory;
        $this->optionResource = $optionResource;
        $this->storeManager = $storeManager;
        $this->linkManagement = $linkManagement;
        $this->productOptionList = $productOptionList;
        $this->linkList = $linkList;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sku, $optionId)
    {
        $product = $this->getProduct($sku);

        /** @var \Magento\Bundle\Model\Option $option */
        $option = $this->type->getOptionsCollection($product)->getItemById($optionId);
        if (!$option || !$option->getId()) {
            throw new NoSuchEntityException(__('Requested option doesn\'t exist'));
        }

        $productLinks = $this->linkList->getItems($product, $optionId);

        /** @var \Credevlab\ConfigurableBundle\Api\Data\OptionInterface $option */
        $optionDataObject = $this->optionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $optionDataObject,
            $option->getData(),
            \Credevlab\ConfigurableBundle\Api\Data\OptionInterface::class
        );
        $optionDataObject->setOptionId($option->getId())
            ->setTitle($option->getTitle() === null ? $option->getDefaultTitle() : $option->getTitle())
            ->setSku($product->getSku())
            ->setProductLinks($productLinks);

        return $optionDataObject;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($sku)
    {
        $product = $this->getProduct($sku);
        return $this->getListByProduct($product);
    }

    /**
     * @param ProductInterface $product
     * @return \Credevlab\ConfigurableBundle\Api\Data\OptionInterface[]
     */
    public function getListByProduct(ProductInterface $product)
    {
        return $this->productOptionList->getItems($product);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Credevlab\ConfigurableBundle\Api\Data\OptionInterface $option)
    {
        try {
            $this->optionResource->delete($option);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\StateException(
                __('Cannot delete option with id %1', $option->getOptionId()),
                $exception
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sku, $optionId)
    {
        $product = $this->getProduct($sku);
        $optionCollection = $this->type->getOptionsCollection($product);
        $optionCollection->setIdFilter($optionId);
        return $this->delete($optionCollection->getFirstItem());
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Credevlab\ConfigurableBundle\Api\Data\OptionInterface $option
    ) {
        $metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);

        $option->setStoreId($product->getStoreId());
        $parentId = $product->getData($metadata->getLinkField());
        $option->setParentId($parentId);

        $optionId = $option->getOptionId();
        $linksToAdd = [];
        $optionCollection = $this->type->getOptionsCollection($product);
        $optionCollection->setIdFilter($option->getOptionId());
        $optionCollection->setProductLinkFilter($parentId);

        /** @var \Magento\Bundle\Model\Option $existingOption */
        $existingOption = $optionCollection->getFirstItem();
        if (!$optionId) {
            $option->setOptionId(null);
        }
        if (!$optionId || $existingOption->getParentId() != $parentId) {
            $option->setDefaultTitle($option->getTitle());
            if (is_array($option->getProductLinks())) {
                $linksToAdd = $option->getProductLinks();
            }
        } else {
            if (!$existingOption->getOptionId()) {
                throw new NoSuchEntityException(__('Requested option doesn\'t exist'));
            }

            $option->setData(array_merge($existingOption->getData(), $option->getData()));
            $this->updateOptionSelection($product, $option);
        }

        try {
            $this->optionResource->save($option);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save option'), $e);
        }

        /** @var \Credevlab\ConfigurableBundle\Api\Data\LinkInterface $linkedProduct */
        foreach ($linksToAdd as $linkedProduct) {
            $this->linkManagement->addChild($product, $option->getOptionId(), $linkedProduct);
        }
        $product->setIsRelationsChanged(true);
        return $option->getOptionId();
    }

    /**
     * Update option selections
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Credevlab\ConfigurableBundle\Api\Data\OptionInterface $option
     * @return $this
     */
    protected function updateOptionSelection(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Credevlab\ConfigurableBundle\Api\Data\OptionInterface $option
    ) {
        $optionId = $option->getOptionId();
        $existingLinks = $this->linkManagement->getChildren($product->getSku(), $optionId);
        $linksToAdd = [];
        $linksToUpdate = [];
        $linksToDelete = [];
        if (is_array($option->getProductLinks())) {
            $productLinks = $option->getProductLinks();
            foreach ($productLinks as $productLink) {
                if (!$productLink->getId() && !$productLink->getSelectionId()) {
                    $linksToAdd[] = $productLink;
                } else {
                    $linksToUpdate[] = $productLink;
                }
            }
            /** @var \Credevlab\ConfigurableBundle\Api\Data\LinkInterface[] $linksToDelete */
            $linksToDelete = $this->compareLinks($existingLinks, $linksToUpdate);
        }
        foreach ($linksToUpdate as $linkedProduct) {
            $this->linkManagement->saveChild($product->getSku(), $linkedProduct);
        }
        foreach ($linksToDelete as $linkedProduct) {
            $this->linkManagement->removeChild(
                $product->getSku(),
                $option->getOptionId(),
                $linkedProduct->getSku()
            );
        }
        foreach ($linksToAdd as $linkedProduct) {
            $this->linkManagement->addChild($product, $option->getOptionId(), $linkedProduct);
        }
        return $this;
    }

    /**
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getProduct($sku)
    {
        $product = $this->productRepository->get($sku, true);
        if ($product->getTypeId() != Type::TYPE_CODE) {
            throw new InputException(__('Only implemented for configurable bundle product'));
        }
        return $product;
    }

    /**
     * Computes the difference between given arrays.
     *
     * @param \Credevlab\ConfigurableBundle\Api\Data\LinkInterface[] $firstArray
     * @param \Credevlab\ConfigurableBundle\Api\Data\LinkInterface[] $secondArray
     *
     * @return array
     */
    private function compareLinks(array $firstArray, array $secondArray)
    {
        $result = [];

        $firstArrayIds = [];
        $firstArrayMap = [];

        $secondArrayIds = [];

        foreach ($firstArray as $item) {
            $firstArrayIds[] = $item->getId();

            $firstArrayMap[$item->getId()] = $item;
        }

        foreach ($secondArray as $item) {
            $secondArrayIds[] = $item->getId();
        }

        foreach (array_diff($firstArrayIds, $secondArrayIds) as $id) {
            $result[] = $firstArrayMap[$id];
        }

        return $result;
    }

    /**
     * Get MetadataPool instance
     * @return MetadataPool
     */
    private function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
        }
        return $this->metadataPool;
    }
}
