<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractCategory
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::category_save';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->categoryFactory->create();
            if (!empty($data['category_id'])) {
                $model->load($data['category_id']);
                if ($data['category_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong category ID: %1.', $data['category_id']));
                }
            } else {
                unset($data['category_id']);
            }
            $model->addData($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('The category has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
                $this->logger->critical($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the category.'));
                $this->logger->critical($e->getMessage());
            }
            $this->dataPersistor->set('blog_category', $data);
            return $resultRedirect->setPath('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
