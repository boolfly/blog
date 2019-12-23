<?php

namespace Boolfly\Blog\Controller\Adminhtml\Tag;

use Boolfly\Blog\Controller\Adminhtml\AbstractTag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class MassDelete extends AbstractTag
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Boolfly_Blog::tag_delete';

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals      = 0;
        try {
            /** @var \Boolfly\Blog\Model\Tag $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the tag(s).'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
