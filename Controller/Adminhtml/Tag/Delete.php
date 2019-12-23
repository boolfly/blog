<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends AbstractTag
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::tag_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $deleteId = (int)$this->getRequest()->getParam('tag_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($deleteId) {
            $tagModel = $this->tagFactory->create();
            try {
                $tagModel->load($deleteId);
                $tagModel->delete();
                $this->messageManager->addSuccessMessage(__('The tag has been deleted successfully'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the tag.'));
                $this->logger->critical($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
