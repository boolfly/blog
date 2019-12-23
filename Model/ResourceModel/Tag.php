<?php

namespace Boolfly\Blog\Model\ResourceModel;

use Boolfly\Blog\Model\Tag as TagModel;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Zend_Db_Select;

class Tag extends AbstractResourceModel
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bf_blog_tag', 'tag_id');
    }

    /**
     * @param $url_key
     * @return string
     * @throws LocalizedException
     */
    public function checkUrlKey($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('tag.tag_id')->limit(1);
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
            ['tag' => $this->getMainTable()]
        )->where('tag.url_key = ?', $url_key);

        return $select;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterDelete(AbstractModel $object)
    {
        /*delete old posts*/
        $table = $this->getTable('bf_blog_tag_post');
        $where = ['tag_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($table, $where);

        /*clean cache for related tags*/
        $this->cleanCache();
        return parent::_afterDelete($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->cleanCache();
        return parent::_afterSave($object);
    }

    /**
     * clean cache for related tags
     */
    protected function cleanCache()
    {
        $this->cacheManager->clean([TagModel::CACHE_TAG . '_all_tags']);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
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
                __('The tag URL key contains capital letters or disallowed symbols.')
            );
        }
        if ($this->isNumericUrlKey($object)) {
            throw new LocalizedException(
                __('The tag URL key cannot be made of only numbers.')
            );
        }
        return parent::_beforeSave($object);
    }
}
