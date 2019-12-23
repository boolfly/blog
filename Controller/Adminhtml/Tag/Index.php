<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Index extends AbstractTag
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::tag';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Tag List'));
        return $resultPage;
    }
}
