<?php

namespace Boolfly\Blog\Controller\Adminhtml\Author;

use Boolfly\Blog\Controller\Adminhtml\AbstractAuthor;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractAuthor
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::author_save';

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->authorFactory->create();
            if (!empty($data['author_id'])) {
                $model->load($data['author_id']);
                if ($data['author_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong author ID: %1.', $data['author_id']));
                }
            } else {
                unset($data['author_id']);
            }
            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('The author has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
                $this->logger->critical($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the author.'));
                $this->logger->critical($e->getMessage());
            }
            $this->dataPersistor->set('blog_author', $data);
            return $resultRedirect->setPath('*/*/edit', ['author_id' => $this->getRequest()->getParam('author_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
