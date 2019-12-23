<?php

namespace Boolfly\Blog\Controller\Tag;

use Boolfly\Blog\Model\TagFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var TagFactory
     */
    private $tagFactory;

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
     * Brand constructor.
     * @param Context $context
     * @param TagFactory $tagFactory
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TagFactory $tagFactory,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->tagFactory = $tagFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $tagId = $this->getRequest()->getParam('id');
        $tag = $this->tagFactory->create()->load($tagId);

        if (!$tag->getId()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->registry->register('chosen_tag', $tag);
        return $this->resultPageFactory->create();
    }
}
