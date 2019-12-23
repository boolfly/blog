<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Edit extends AbstractCategory
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::category_save';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('category_id');
        $categoryModel = $this->categoryFactory->create();
        if ($categoryId) {
            $categoryModel->load($categoryId);
            if (!$categoryModel->getId()) {
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addErrorMessage(__('This category no longer exists!'));
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $categoryModel->addData($data);
        }
        $this->coreRegistry->register('current_category', $categoryModel);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $pageTitle = $categoryId ? __('Edit Category') : __('Add Category');
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
