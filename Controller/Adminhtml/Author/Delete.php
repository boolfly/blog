<?php

namespace Boolfly\Blog\Controller\Adminhtml\Author;

use Boolfly\Blog\Controller\Adminhtml\AbstractAuthor;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends AbstractAuthor
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::author_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $deleteId = (int)$this->getRequest()->getParam('author_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($deleteId) {
            $authorModel = $this->authorFactory->create();
            try {
                $authorModel->load($deleteId);
                $authorModel->delete();
                $this->messageManager->addSuccessMessage(__('The author has been deleted successfully'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the author.'));
                $this->logger->critical($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
