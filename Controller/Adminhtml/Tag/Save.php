<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractTag
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::tag_save';

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
            $model = $this->tagFactory->create();
            if (!empty($data['tag_id'])) {
                $model->load($data['tag_id']);
                if ($data['tag_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong tag ID: %1.', $data['tag_id']));
                }
            } else {
                unset($data['tag_id']);
            }
            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('The tag has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
                $this->logger->critical($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the tag.'));
                $this->logger->critical($e->getMessage());
            }
            $this->dataPersistor->set('blog_tag', $data);
            return $resultRedirect->setPath('*/*/edit', ['tag_id' => $this->getRequest()->getParam('tag_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
