<?php

namespace Boolfly\Blog\Model\ResourceModel\Tag;

use Boolfly\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'tag_id';
    protected $_eventPrefix = 'bf_blog_tag_collection';
    protected $_eventObject = 'bf_blog_tag_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\Tag', 'Boolfly\Blog\Model\ResourceModel\Tag');
    }

    /**
     * Join Left bf_blog_tag_post table
     */
    public function joinPostTable()
    {
        $this->addFilterToMap('tag_id', 'main_table.tag_id');
        $this->getSelect()->joinLeft(
            ['post_tag' => $this->getTable('bf_blog_tag_post')],
            'main_table.tag_id = post_tag.tag_id',
            'post_id'
        );
    }
}
