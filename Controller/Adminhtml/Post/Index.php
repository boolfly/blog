<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Index extends AbstractPost
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::post';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Post List'));
        return $resultPage;
    }
}
