<?php

namespace Boolfly\Blog\Controller\Adminhtml;

use Boolfly\Blog\Model\TagFactory;
use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractTag extends Action
{
    /**
     * Forward factory for result
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var TagFactory
     */
    protected $tagFactory;

    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractTag constructor.
     * @param Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param TagFactory $tagFactory
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        TagFactory $tagFactory,
        Registry $registry,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Filter $filter,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->tagFactory = $tagFactory;
        $this->coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
    }
}