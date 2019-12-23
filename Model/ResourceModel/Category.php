<?php

namespace Boolfly\Blog\Model\ResourceModel;

use Boolfly\Blog\Model\Category as CategoryModel;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Zend_Db_Select;

class Category extends AbstractResourceModel
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bf_blog_category', 'category_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    protected function lookupStoreIds($id)
    {
        return $this->getStoreIds('bf_blog_category_store', 'category_id', $id);
    }

    /**
     * @param $objectId
     * @param $storeIds
     */
    protected function updateStores($objectId, $storeIds)
    {
        $table = $this->getTable('bf_blog_category_store');
        $oldStoreIds = $this->lookupStoreIds($objectId);
        $newStoreIds = (array)$storeIds;
        $insertData = array_diff($newStoreIds, $oldStoreIds);
        $deleteData = array_diff($oldStoreIds, $newStoreIds);

        if ($insertData) {
            $data = [];
            foreach ($insertData as $storeId) {
                $data[] = ['category_id' => (int)$objectId, 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($deleteData) {
            $where = ['category_id = ?' => (int)$objectId, 'store_id IN (?)' => $deleteData];
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->updateStores($object->getId(), $object->getData('store_ids'));
        $this->cleanCache();
        return parent::_afterSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $storeIds = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $storeIds);
        }
        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $oldStoreIds = $this->lookupStoreIds($object->getId());

        /*delete old stores*/
        $table = $this->getTable('bf_blog_category_store');
        if ($oldStoreIds) {
            $where = ['category_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $oldStoreIds];
            $this->getConnection()->delete($table, $where);
        }

        /*delete old posts*/
        $table = $this->getTable('bf_blog_post_category');
        $where = ['category_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($table, $where);

        /*clean cache for related tags*/
        $this->cleanCache();
        return parent::_afterDelete($object);
    }

    /**
     * clean cache for related tags
     */
    protected function cleanCache()
    {
        $this->cacheManager->clean([CategoryModel::CACHE_TAG . '_all_categories']);
    }

    /**
     * @param $url_key
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function checkUrlKeyAndGetId($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key, 1);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('category.category_id');
        $categoryIds = $this->getConnection()->fetchCol($select);

        return $this->getObjectIdByStore($categoryIds, 'bf_blog_category_store', 'category_id');
    }

    /**
     * @param $url_key
     * @param null $isActive
     * @return Select
     * @throws LocalizedException
     */
    protected function getLoadByUrlKeySelect($url_key, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['category' => $this->getMainTable()]
        )->where(
            'category.url_key = ?',
            $url_key
        );
        if (!is_null($isActive)) {
            $select->where('category.is_active = ?', $isActive);
        }
        return $select;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractResourceModel
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /*Get ids of existing categories which have url keys same as AbstractModel object's url key*/
        $existingIds = $this->findIdsByUrlKey($object->getData('url_key'));

        if (!$this->checkUniqueUrlKey($object, $existingIds, 'bf_blog_category_store', 'category_id')) {
            throw new LocalizedException(
                __('The url key must be unique.')
            );
        }

        if (!$this->isValidUrlKey($object)) {
            throw new LocalizedException(
                __('The category URL key contains capital letters or disallowed symbols.')
            );
        }
        if ($this->isNumericUrlKey($object)) {
            throw new LocalizedException(
                __('The category URL key cannot be made of only numbers.')
            );
        }
        return parent::_beforeSave($object);
    }

    /**
     * Get ids of existing categories which have the url keys same as $url_key.
     *
     * @param $url_key
     * @return array
     * @throws LocalizedException
     */
    public function findIdsByUrlKey($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('category.category_id');
        return $this->getConnection()->fetchCol($select);
    }
}
