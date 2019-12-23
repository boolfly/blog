<?php

namespace Boolfly\Blog\Controller\Adminhtml\Author;

use Boolfly\Blog\Controller\Adminhtml\AbstractAuthor;
use Boolfly\Blog\Model\Author;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends AbstractAuthor
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::author_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals      = 0;
        try {
            /** @var Author $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the author(s).'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
