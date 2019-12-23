<?php

namespace Boolfly\Blog\Model\ResourceModel\Category;

use Boolfly\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'category_id';
    protected $_eventPrefix = 'bf_blog_category_collection';
    protected $_eventObject = 'bf_blog_category_collection';
    protected $flagStoreFilter = false;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\Category', 'Boolfly\Blog\Model\ResourceModel\Category');
    }

    /**
     * @param $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        $this->storeFilter('category_id', 'bf_blog_category_store', $store);
        return $this;
    }

    /**
     * Join Left bf_blog_post_category table
     */
    public function joinPostTable()
    {
        $this->addFilterToMap('category_id', 'main_table.category_id');
        $this->getSelect()->joinLeft(
            ['post_category' => $this->getTable('bf_blog_post_category')],
            'main_table.category_id = post_category.category_id',
            'post_id'
        );
    }

    /**
     * @return AbstractCollection
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('bf_blog_category_store', 'category_id');
        return parent::_afterLoad();
    }
}
