<?php

namespace Boolfly\Blog\Model\ResourceModel;

use Boolfly\Blog\Model\Post as PostModel;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Zend_Db_Select;

class Author extends AbstractResourceModel
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bf_blog_author', 'author_id');
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterDelete(AbstractModel $object)
    {
        /*delete old posts*/
        $table = $this->getTable('bf_blog_post');
        $where = ['author_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($table, $where);
        $this->cleanCache($object->getId());

        return parent::_afterDelete($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->cleanCache($object->getId());

        return parent::_afterSave($object);
    }

    /**
     * clean cache for related tags
     * @param $authodId
     */
    protected function cleanCache($authodId)
    {
        $cacheTags = [];
        foreach ($this->lookupPostIds($authodId) as $postId) {
            array_push($cacheTags, PostModel::CACHE_TAG . '_' . $postId);
        }
        array_push(
            $cacheTags,
            PostModel::CACHE_TAG . '_all_posts',
            PostModel::CACHE_TAG . '_' . 'widget_posts'
        );
        $this->cacheManager->clean($cacheTags);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $isUniqueUrlKey = $this->checkUrlKey($object->getData('url_key'));
        if ($object->isObjectNew()) {
            if ($isUniqueUrlKey) {
                throw new LocalizedException(
                    __('The url key must be unique.')
                );
            }
        } else {
            if ($isUniqueUrlKey && $isUniqueUrlKey != $object->getId()) {
                throw new LocalizedException(
                    __('The url key must be unique.')
                );
            }
        }

        if (!$this->isValidUrlKey($object)) {
            throw new LocalizedException(
                __('The author URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericUrlKey($object)) {
            throw new LocalizedException(
                __('The author URL key cannot be made of only numbers.')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param $url_key
     * @return string
     * @throws LocalizedException
     */
    public function checkUrlKey($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('author.author_id')->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $url_key
     * @return Select
     * @throws LocalizedException
     */
    protected function getLoadByUrlKeySelect($url_key)
    {
        $select = $this->getConnection()->select()->from(
            ['author' => $this->getMainTable()]
        )->where('author.url_key = ?', $url_key);
        return $select;
    }

    /**
     * @param $authorId
     * @return array
     */
    protected function lookupPostIds($authorId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_post'),
            'post_id'
        )->where('author_id = ?', (int)$authorId);

        return $connection->fetchCol($select);
    }
}
