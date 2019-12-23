<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Exception;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends AbstractPost
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::post_delete';

    /**
     * @return Redirect
     * @throws Exception
     */
    public function execute()
    {
        $deleteId = (int)$this->getRequest()->getParam('post_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($deleteId) {
            $postModel = $this->postFactory->create();
            try {
                $postModel->load($deleteId);
                $postModel->delete();
                $this->messageManager->addSuccessMessage(__('The post has been deleted successfully'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the post.'));
                $this->logger->critical($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
