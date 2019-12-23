<?php

namespace Boolfly\Blog\Model\ResourceModel;

use Boolfly\Blog\Model\Post as PostModel;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Zend_Db_Select;

class Post extends AbstractResourceModel
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bf_blog_post', 'post_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    protected function lookupStoreIds($id)
    {
        return $this->getStoreIds('bf_blog_post_store', 'post_id', $id);
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    protected function lookupCategoryIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_post_category'),
            'category_id'
        )->where('post_id = ?', (int)$id);
        return $connection->fetchCol($select);
    }

    /**
     * Get tag ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    protected function lookupTagIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_tag_post'),
            'tag_id'
        )->where('post_id = ?', (int)$id);
        return $connection->fetchCol($select);
    }

    /**
     * Get product ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    protected function lookupProductIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_post_product'),
            'product_id'
        )->where('post_id = ?', (int)$id);
        return $connection->fetchCol($select);
    }

    /**
     * @param $id
     * @return array
     */
    protected function lookupRelatedPostIds($id)
    {
        $tagIds = $this->lookupTagIds($id);
        $relatedPostIds = [];
        foreach ($tagIds as $tagId) {
            $postIds = $this->getFilteredPostIds($tagId, $id);
            foreach ($postIds as $postId) {
                $relatedPostIds[] = (int)$postId;
            }
        }
        return array_unique($relatedPostIds);
    }

    /**
     * @param $tagId
     * @param $postId
     * @return array
     */
    protected function getFilteredPostIds($tagId, $postId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_tag_post'),
            'post_id'
        )->where(
            'tag_id = :tag_id AND (post_id <> :post_id)'
        );
        $binds = [':tag_id' => (int)$tagId,':post_id' => (int)$postId];
        return $connection->fetchCol($select, $binds);
    }

    /**
     * Update StoreViews
     *
     * @param $objectId
     * @param $storeIds
     */
    protected function updateStores($objectId, $storeIds)
    {
        $table = $this->getTable('bf_blog_post_store');
        $oldStoreIds = $this->lookupStoreIds($objectId);
        $newStoreIds = (array)$storeIds;
        $insertData = array_diff($newStoreIds, $oldStoreIds);
        $deleteData = array_diff($oldStoreIds, $newStoreIds);

        if ($insertData) {
            $data = [];
            foreach ($insertData as $storeId) {
                $data[] = ['post_id' => (int)$objectId, 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($deleteData) {
            $where = ['post_id = ?' => (int)$objectId, 'store_id IN (?)' => $deleteData];
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * Update Categories
     *
     * @param $objectId
     * @param $categoryIds
     */
    protected function updateCategories($objectId, $categoryIds)
    {
        $table = $this->getTable('bf_blog_post_category');
        $oldCategoryIds = $this->lookupCategoryIds($objectId);
        $newCategoryIds = (array)$categoryIds;
        $insertData = array_diff($newCategoryIds, $oldCategoryIds);
        $deleteData = array_diff($oldCategoryIds, $newCategoryIds);

        if ($insertData) {
            $data = [];
            foreach ($insertData as $categoryId) {
                $data[] = ['post_id' => (int)$objectId, 'category_id' => (int)$categoryId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($deleteData) {
            $where = ['post_id = ?' => (int)$objectId, 'category_id IN (?)' => $deleteData];
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * Update Tags
     *
     * @param $objectId
     * @param $tagIds
     */
    protected function updateTags($objectId, $tagIds)
    {
        $table = $this->getTable('bf_blog_tag_post');
        $oldTagIds = $this->lookupTagIds($objectId);
        $newTagIds = (array)$tagIds;
        $insertData = array_diff($newTagIds, $oldTagIds);
        $deleteData = array_diff($oldTagIds, $newTagIds);

        if ($insertData) {
            $data = [];
            foreach ($insertData as $tagId) {
                $data[] = ['post_id' => (int)$objectId, 'tag_id' => (int)$tagId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($deleteData) {
            $where = ['post_id = ?' => (int)$objectId, 'tag_id IN (?)' => $deleteData];
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * Update Products
     *
     * @param $objectId
     * @param $productIds
     */
    protected function updateProducts($objectId, $productIds)
    {
        $table = $this->getTable('bf_blog_post_product');
        $oldProductIds = $this->lookupProductIds($objectId);
        $newProductIds = (array)$productIds;
        $insertData = array_diff($newProductIds, $oldProductIds);
        $deleteData = array_diff($oldProductIds, $newProductIds);

        if ($insertData) {
            $data = [];
            foreach ($insertData as $productId) {
                $data[] = ['post_id' => (int)$objectId, 'product_id' => (int)$productId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($deleteData) {
            $where = ['post_id = ?' => (int)$objectId, 'product_id IN (?)' => $deleteData];
            $this->getConnection()->delete($table, $where);
        }
    }

    /**
     * Update data for related tables after saving
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->updateStores($object->getId(), $object->getData('store_ids'));
        $this->updateCategories($object->getId(), $object->getData('category_ids'));
        $this->updateTags($object->getId(), $object->getData('tag_ids'));
        $this->updateProducts($object->getId(), $object->getData('product_ids'));
        $this->cleanCache();
        return parent::_afterSave($object);
    }

    /**
     * clean cache for related tags
     */
    protected function cleanCache()
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        $this->cacheManager->clean([PostModel::CACHE_TAG . '_all_posts',PostModel::CACHE_TAG . '_' . 'widget_posts']);
    }

    /**
     * Prepare data for object after loading
     *
     * @param AbstractModel $object
     * @return AbstractDb
     * @throws NoSuchEntityException
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setData('store_ids', $this->lookupStoreIds($object->getId()));
            $object->setData('category_ids', $this->lookupCategoryIds(($object->getId())));
            $object->setData('tag_ids', $this->lookupTagIds($object->getId()));
            $object->setData('product_ids', $this->lookupProductIds($object->getId()));
            $object->setData('author_url', $this->getAuthorUrlKey($object->getAuthorId()));
            $object->setData('author_full_name', $this->lookupAuthorFullName($object->getAuthorId()));
            $object->setData('related_post_ids', $this->lookupRelatedPostIds($object->getId()));
        }
        return parent::_afterLoad($object);
    }

    /**
     * Prepare Stores's data to delete
     *
     * @param $objectId
     */
    protected function prepareStoresToDelete($objectId)
    {
        $table = $this->getTable('bf_blog_post_store');
        $where = ['post_id = ?' => (int)$objectId];
        $this->getConnection()->delete($table, $where);
    }

    /**
     * Prepare Categories's data to delete
     *
     * @param $objectId
     */
    protected function prepareCategoriesToDelete($objectId)
    {
        $table = $this->getTable('bf_blog_post_category');
        $where = ['post_id = ?' => (int)$objectId];
        $this->getConnection()->delete($table, $where);
    }

    /**
     * Prepare Tags's data to delete
     *
     * @param $objectId
     */
    protected function prepareTagsToDelete($objectId)
    {
        $table = $this->getTable('bf_blog_tag_post');
        $where = ['post_id = ?' => (int)$objectId];
        $this->getConnection()->delete($table, $where);
    }

    /**
     * Prepare Products's data to delete
     *
     * @param $objectId
     */
    protected function prepareProductsToDelete($objectId)
    {
        $table = $this->getTable('bf_blog_post_product');
        $where = ['post_id = ?' => (int)$objectId];
        $this->getConnection()->delete($table, $where);
    }

    /**
     * Delete data from related tables after deleting
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->prepareStoresToDelete($object->getId());
        $this->prepareCategoriesToDelete($object->getId());
        $this->prepareTagsToDelete($object->getId());
        $this->prepareProductsToDelete($object->getId());
        $this->cleanCache();
        return parent::_afterDelete($object);
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
        $select->reset(Zend_Db_Select::COLUMNS)->columns('post.post_id');
        $postIds = $this->getConnection()->fetchCol($select);

        return $this->getObjectIdByStore($postIds, 'bf_blog_post_store', 'post_id');
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
            ['post' => $this->getMainTable()]
        )->where(
            'post.url_key = ?',
            $url_key
        );
        if (!is_null($isActive)) {
            $select->where('post.is_active = ?', $isActive);
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
        /*Get ids of existing posts which have url keys same as AbstractModel object's url key*/
        $existingIds = $this->findIdsByUrlKey($object->getData('url_key'));

        if (!$this->checkUniqueUrlKey($object, $existingIds, 'bf_blog_post_store', 'post_id')) {
            throw new LocalizedException(
                __('The url key must be unique.')
            );
        }

        if (!$this->isValidUrlKey($object)) {
            throw new LocalizedException(
                __('The post URL key contains capital letters or disallowed symbols.')
            );
        }
        if ($this->isNumericUrlKey($object)) {
            throw new LocalizedException(
                __('The post URL key cannot be made of only numbers.')
            );
        }
        return parent::_beforeSave($object);
    }

    /**
     * Get ids of existing posts which have the url keys same as $url_key.
     *
     * @param $url_key
     * @return array
     * @throws LocalizedException
     */
    protected function findIdsByUrlKey($url_key)
    {
        $select = $this->getLoadByUrlKeySelect($url_key);
        $select->reset(Zend_Db_Select::COLUMNS)->columns('post.post_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param $id
     * @return string
     */
    public function lookupAuthorFullName($authorId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_author'),
            ['author_fullname' => "CONCAT(first_name, ' ', last_name)"]
        )->where('author_id = ?', (int)$authorId);
        return $connection->fetchOne($select);
    }

    /**
     * @param $authorId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAuthorUrlKey($authorId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('bf_blog_author'),
            'url_key'
        )->where('author_id = ?', (int)$authorId);
        $urlKey = $connection->fetchOne($select);
        $prefixUrl = $this->config->getRouter();
        $postUrl = (!empty($prefixUrl)) ? '/' . trim($prefixUrl) . '/author/' . $urlKey : '/bf_blog' . '/author/' . $urlKey;
        return $postUrl;
    }
}
