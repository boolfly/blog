<?php

namespace Boolfly\Blog\Block;

use Boolfly\Blog\Model\Category;
use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\ResourceModel\Category\Collection;
use Boolfly\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class CategoryList extends AbstractBlock
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * CategoryList constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        CollectionFactory $collectionFactory,
        Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $config, $registry, $pageConfig, $data);
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCategoryList()
    {
        $limit = $this->config->getNumberCategories();
        $categoryList = $this->collectionFactory->create();
        $categoryList->addFieldToFilter('is_active', Category::STATUS_ENABLED);
        $categoryList->addStoreFilter($this->_storeManager->getStore());
        if ($limit && (int)$limit != 0) {
            $categoryList->setOrder('category_id');
            $categoryList->setPageSize((int)$limit);
        }
        return $categoryList;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return  [Category::CACHE_TAG . '_all_categories'];
    }
}
