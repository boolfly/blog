<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Add extends AbstractPost
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::post_save';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
