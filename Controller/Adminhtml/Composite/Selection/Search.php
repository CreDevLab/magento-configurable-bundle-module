<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Credevlab\Composite\Controller\Adminhtml\Bundle\Selection;

use Magento\Catalog\Controller\Adminhtml\Product;

class Search extends Product
{
    /**
     * @return mixed
     */
    public function execute()
    {
        return $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                \Credevlab\Composite\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search::class
            )->setIndex(
                $this->getRequest()->getParam('index')
            )->setFirstShow(
                true
            )->toHtml()
        );
    }
}
