<?php

namespace Boolfly\Blog\Model\Config\Source\Widget;

use Boolfly\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Categories implements OptionSourceInterface
{
    const DEFAULT_VALUE = 0;
    const DEFAULT_LABEL = 'All categories';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var
     */
    protected $option;

    /**
     * Categories constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if (!$this->option) {
            $categories = $this->collectionFactory->create();
            $this->option = [];
            array_push($this->option, ['value' => self::DEFAULT_VALUE, 'label' => __(self::DEFAULT_LABEL)]);
            foreach ($categories as $category) {
                array_push($this->option, ['value' => $category->getId(), 'label' => __($category->getName())]);
            }
        }
        return $this->option;
    }
}
