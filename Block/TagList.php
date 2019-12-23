<?php

namespace Boolfly\Blog\Block;

use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\ResourceModel\Tag\Collection;
use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Boolfly\Blog\Model\Tag;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class TagList extends AbstractBlock
{
    const NUMBER_OF_TAG = 10;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * TagList constructor.
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
    public function getTagList()
    {
        $limit = $this->config->getNumberTags() ?: self::NUMBER_OF_TAG;
        $tagList = $this->collectionFactory->create();
        $tagList->joinPostTable();
        $tagList->getSelect()
            ->columns(['posts_count' => new \Zend_Db_Expr('COUNT(post_id)')])
            ->group('main_table.tag_id');
        $tagList->setOrder('posts_count');
        $tagList->setPageSize((int)$limit);
        return $tagList;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return  [Tag::CACHE_TAG . '_all_tags'];
    }
}
