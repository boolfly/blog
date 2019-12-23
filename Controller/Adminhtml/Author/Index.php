<?php

namespace Boolfly\Blog\Controller\Adminhtml\Author;

use Boolfly\Blog\Controller\Adminhtml\AbstractAuthor;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Index extends AbstractAuthor
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::author';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Author List'));
        return $resultPage;
    }
}
