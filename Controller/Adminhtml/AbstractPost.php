<?php

namespace Boolfly\Blog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Boolfly\Blog\Model\PostFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory;

abstract class AbstractPost extends Action
{
    /**
     * Forward factory for result
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * AbstractPost constructor.
     * @param Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PostFactory $postFactory
     * @param LoggerInterface $logger
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        PostFactory $postFactory,
        LoggerInterface $logger,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Filter $filter,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->postFactory = $postFactory;
        $this->logger = $logger;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->dataPersistor = $dataPersistor;
    }
}
