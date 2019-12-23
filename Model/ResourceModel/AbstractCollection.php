<?php

namespace Boolfly\Blog\Model\ResourceModel;

class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $flagStoreFilter = false;

    /**
     * @param $idColumn
     * @param $storeTable
     * @param $store
     * @return void
     */
    public function storeFilter($idColumn, $storeTable, $store)
    {
        if (!$this->flagStoreFilter) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $store = [$store->getId()];
            }
            if (!is_array($store)) {
                $store = [$store];
            }
            $this->addFilterToMap($idColumn, 'main_table.' . $idColumn);
            $this->getSelect()->join(
                ['store_table' => $this->getTable($storeTable)],
                'main_table.' . $idColumn . ' = store_table.' . $idColumn,
                []
            )->where('store_table.store_id in (?,0)', $store)
                ->group('main_table.' . $idColumn);
            $this->flagStoreFilter = true;
        }
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['blog_entity_store' => $this->getTable($tableName)])
                ->where('blog_entity_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }
}
