<?php

namespace Boolfly\Blog\Controller\Adminhtml\Author;

use Boolfly\Blog\Controller\Adminhtml\AbstractAuthor;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Edit extends AbstractAuthor
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::author_save';

    /**
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $authorId = (int)$this->getRequest()->getParam('author_id');
        $authorModel = $this->authorFactory->create();
        if ($authorId) {
            $authorModel->load($authorId);
            if (!$authorModel->getId()) {
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addErrorMessage(__('This author no longer exists!'));
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $authorModel->addData($data);
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $pageTitle = $authorId ? __('Edit Author') : __('Add Author');
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
