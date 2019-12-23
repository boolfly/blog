<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Index extends AbstractCategory
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::category';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Category List'));
        return $resultPage;
    }
}
