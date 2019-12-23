<?php

namespace Boolfly\Blog\Controller\Category;

use Boolfly\Blog\Model\CategoryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * ViewCategory constructor.
     * @param Context $context
     * @param CategoryFactory $categoryFactory
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('id');
        $category = $this->categoryFactory->create()->load($categoryId);

        if (!$category->getId()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->registry->register('chosen_category', $category);
        return $this->resultPageFactory->create();
    }
}
