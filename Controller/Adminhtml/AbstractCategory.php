<?php

namespace Boolfly\Blog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Boolfly\Blog\Model\CategoryFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Boolfly\Blog\Model\ResourceModel\Category\CollectionFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractCategory extends Action
{
    /**
     * Forward factory for result
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

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
     * AbstractCategory constructor.
     * @param Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param CategoryFactory $categoryFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        CategoryFactory $categoryFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->categoryFactory = $categoryFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
    }
}
