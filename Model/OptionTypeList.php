<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\ConfigurableBundle\Model;

class OptionTypeList implements \Credevlab\ConfigurableBundle\Api\ProductOptionTypeListInterface
{
    /**
     * @var Source\Option\Type
     */
    protected $types;

    /**
     * @var \Credevlab\ConfigurableBundle\Api\Data\OptionTypeInterfaceFactory
     */
    protected $typeFactory;

    /**
     * @param Source\Option\Type $type
     * @param \Credevlab\ConfigurableBundle\Api\Data\OptionTypeInterfaceFactory $typeFactory
     */
    public function __construct(
        \Magento\Bundle\Model\Source\Option\Type $type,
        \Credevlab\ConfigurableBundle\Api\Data\OptionTypeInterfaceFactory $typeFactory
    ) {
        $this->types = $type;
        $this->typeFactory = $typeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $optionList = $this->types->toOptionArray();

        /** @var \Credevlab\ConfigurableBundle\Api\Data\OptionTypeInterface[] $typeList */
        $typeList = [];
        foreach ($optionList as $option) {
            $typeList[] = $this->typeFactory->create()->setCode($option['value'])->setLabel($option['label']);
        }
        return $typeList;
    }
}
