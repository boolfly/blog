<?php

namespace Boolfly\Blog\Controller\Adminhtml\Category;

use Boolfly\Blog\Controller\Adminhtml\AbstractCategory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class MassDelete extends AbstractCategory
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
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals      = 0;
        try {
            /** @var \Boolfly\Blog\Model\Category $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the category(s).'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
