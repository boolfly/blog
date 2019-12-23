<?php

namespace Boolfly\Blog\Ui\Component\Listing\Column;

use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Store
 */
class Tag extends Column
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    protected $resource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param CollectionFactory $collectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        CollectionFactory $collectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->getTags($item['post_id']);
            }
        }

        return $dataSource;
    }

    /**
     * @param $postId
     * @return array
     */
    protected function getTags($postId)
    {
        $tags = $this->collectionFactory->create();
        $tags->joinPostTable();
        $tags->addFieldToFilter('post_id', (int)$postId);
        $tagNames = $tags->getColumnValues('name');
        return $tagNames;
    }
}
