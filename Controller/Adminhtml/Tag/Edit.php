<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Edit extends AbstractTag
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
        $tagId = (int)$this->getRequest()->getParam('tag_id');
        $tagModel = $this->tagFactory->create();
        if ($tagId) {
            $tagModel->load($tagId);
            if (!$tagModel->getId()) {
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addErrorMessage(__('This tag no longer exists!'));
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $tagModel->addData($data);
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $pageTitle = $tagId ? __('Edit Tag') : __('Add Tag');
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
