<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Add extends AbstractCategory
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::category_save';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
