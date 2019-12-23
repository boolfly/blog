<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Edit extends AbstractPost
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
        $postId = (int)$this->getRequest()->getParam('post_id');
        $postModel = $this->postFactory->create();
        if ($postId) {
            $postModel->load($postId);
            if (!$postModel->getId()) {
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addErrorMessage(__('This post no longer exists!'));
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $postModel->addData($data);
        }
        $this->coreRegistry->register('current_post', $postModel);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Blog::bf_blog');
        $pageTitle = $postId ? __('Edit Post') : __('Add Post');
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
