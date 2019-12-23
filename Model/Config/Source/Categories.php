<?php

namespace Boolfly\Blog\Model\Config\Source;

use Boolfly\Blog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Categories implements OptionSourceInterface
{
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
            foreach ($categories as $category) {
                array_push($this->option, ['value' => $category->getId(), 'label' => __($category->getName())]);
            }
        }
        return $this->option;
    }
}
