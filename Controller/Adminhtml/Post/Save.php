<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractPost
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::post_save';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->postFactory->create();
            if (!empty($data['post_id'])) {
                $model->load($data['post_id']);
                if ($data['post_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong post ID: %1.', $data['post_id']));
                }
            } else {
                unset($data['post_id']);
            }
            if (empty($data['products'])) {
                $model->setData('product_ids', []);
            } else {
                $model->prepareProductData($data['products']);
            }
            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('The post has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
                $this->logger->critical($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the post.'));
                $this->logger->critical($e->getMessage());
            }
            $this->dataPersistor->set('blog_post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
