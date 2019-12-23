<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class MassDelete extends AbstractPost
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::post_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws Exception
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals      = 0;
        try {
            /** @var \Boolfly\Blog\Model\Post $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the post(s).'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
