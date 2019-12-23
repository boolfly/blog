<?php

namespace Boolfly\Blog\Model\Config\Source;

use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Tags implements OptionSourceInterface
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
     * Tags constructor.
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
            $tags = $this->collectionFactory->create();
            $this->option = [];
            foreach ($tags as $tag) {
                array_push($this->option, ['value' => $tag->getId(), 'label' => __($tag->getName())]);
            }
        }
        return $this->option;
    }
}
