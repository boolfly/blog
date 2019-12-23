<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Add extends AbstractTag
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::tag_save';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
