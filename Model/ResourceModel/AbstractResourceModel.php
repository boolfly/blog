<?php

namespace Boolfly\Blog\Model\ResourceModel;

use Boolfly\Blog\Model\Config\Config;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class AbstractResourceModel extends AbstractDb
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var CacheContext
     */
    protected $cacheContext;

    /**
     * Application Cache Manager
     *
     * @var CacheInterface
     */
    protected $cacheManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AbstractResourceModel constructor.
     * @param Context $context
     * @param Config $config
     * @param EventManager $eventManager
     * @param CacheContext $cacheContext
     * @param CacheInterface $cacheManager
     * @param StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Config $config,
        EventManager $eventManager,
        CacheContext $cacheContext,
        CacheInterface $cacheManager,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->cacheContext = $cacheContext;
        $this->cacheManager = $cacheManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * @param AbstractModel $object
     * @return false|int
     */
    protected function isValidUrlKey(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * @param AbstractModel $object
     * @return false|int
     */
    protected function isNumericUrlKey(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Check if url key of the object is not same as any existing objects's url keys(in the same store view).
     *
     * @param AbstractModel $object
     * @param $existingIds
     * @param $storeTable
     * @param $columnToFilter
     * @return bool
     */
    protected function checkUniqueUrlKey(AbstractModel $object, $existingIds, $storeTable, $columnToFilter)
    {
        if (count($existingIds)) {
            foreach ($existingIds as $existingId) {

                /*Array of existing item's store ids*/
                $existingStoreIds = (array)$this->getStoreIds($storeTable, $columnToFilter, $existingId);

                /*Array of AbstractModel object's store ids*/
                $newStoreIds = (array)$object->getStoreIds();

                /*Check intersection of store ids (between AbstractModel object and item which is stored in DB)*/
                $storeIntersection = $this->checkStoreIntersection($existingStoreIds, $newStoreIds);

                if ($object->isObjectNew()) {
                    if ($storeIntersection) {
                        return false;
                    }
                } else {
                    if ($storeIntersection && !in_array($object->getId(), $existingIds)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param $tableName
     * @param $columnToFilter
     * @param $id
     * @return array
     */
    public function getStoreIds($tableName, $columnToFilter, $id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable($tableName),
            'store_id'
        )->where($columnToFilter . ' = ?', (int)$id);
        return $connection->fetchCol($select);
    }

    /**
     * Check check intersection of two arrays which contain store ids
     *
     * Return true if default store id(0) exists in $existingStoreIds/$newStoreIds
     * Return true if there is intersection of $existingStoreIds and $newStoreIds
     * Return false if both of the above cases are incorrect
     *
     * @param $existingStoreIds
     * @param $newStoreIds
     * @return bool
     */
    protected function checkStoreIntersection($existingStoreIds, $newStoreIds)
    {
        if (in_array(Store::DEFAULT_STORE_ID, $existingStoreIds) ||
            in_array(Store::DEFAULT_STORE_ID, $newStoreIds)) {
            return true;
        }

        $intersection = array_intersect($existingStoreIds, $newStoreIds);

        /*Check if there is intersection of two arrays*/
        if (count($intersection)) {
            return true;
        }

        return false;
    }

    /**
     * @param $objectIds
     * @param $storeTable
     * @param $columnToFilter
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getObjectIdByStore($objectIds, $storeTable, $columnToFilter)
    {
        $checkedId = 0;

        if (count($objectIds)) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            foreach ($objectIds as $objectId) {
                $storeIds = $this->getStoreIds($storeTable, $columnToFilter, $objectId);
                if (in_array($currentStoreId, $storeIds) || in_array(Store::DEFAULT_STORE_ID, $storeIds)) {
                    $checkedId = $objectId;
                    break;
                }
            }
        }

        return $checkedId;
    }
}
