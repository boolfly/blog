<?php

namespace Boolfly\Blog\Ui\Component\Listing\Column;

use Boolfly\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param CollectionFactory $collectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        CollectionFactory $collectionFactory,
        array $components = [],
        array $data = []
    ) {
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
                $item[$this->getData('name')] = $this->getCategories($item['post_id']);
            }
        }

        return $dataSource;
    }

    /**
     * @param $postId
     * @return array
     */
    protected function getCategories($postId)
    {
        $categories = $this->collectionFactory->create();
        $categories->joinPostTable();
        $categories->addFieldToFilter('post_id', (int)$postId);
        $categoryNames = $categories->getColumnValues('name');
        return $categoryNames;
    }
}
