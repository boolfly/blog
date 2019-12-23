<?php

namespace Boolfly\Blog\Model\ResourceModel\Author;

use Boolfly\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'author_id';
    protected $_eventPrefix = 'bf_blog_author_collection';
    protected $_eventObject = 'bf_blog_author_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\Author', 'Boolfly\Blog\Model\ResourceModel\Author');
    }
}
