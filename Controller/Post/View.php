<?php

namespace Boolfly\Blog\Controller\Post;

use Boolfly\Blog\Model\PostFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var PostFactory
     */
    private $postFactory;

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
     * @param PostFactory $postFactory
     * @param Registry $registry
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->postFactory = $postFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
        $post = $this->postFactory->create()->load($postId);

        if (!$post->getId()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->registry->register('chosen_post', $post);
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
