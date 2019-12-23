<?php

namespace Boolfly\Blog\Controller\Author;

use Boolfly\Blog\Model\AuthorFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var AuthorFactory
     */
    private $authorFactory;

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
     * @param AuthorFactory $authorFactory
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        AuthorFactory $authorFactory,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->authorFactory = $authorFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $authorId = $this->getRequest()->getParam('id');
        $author = $this->authorFactory->create()->load($authorId);

        if (!$author->getId()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->registry->register('chosen_author', $author);
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
