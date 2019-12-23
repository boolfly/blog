<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends AbstractCategory
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::category_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $deleteId = (int)$this->getRequest()->getParam('category_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($deleteId) {
            $categoryModel = $this->categoryFactory->create();
            try {
                $categoryModel->load($deleteId);
                $categoryModel->delete();
                $this->messageManager->addSuccessMessage(__('The category has been deleted successfully'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the category.'));
                $this->logger->critical($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
