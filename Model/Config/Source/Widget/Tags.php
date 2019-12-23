<?php

namespace Boolfly\Blog\Model\Config\Source\Widget;

use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Tags implements OptionSourceInterface
{
    const DEFAULT_VALUE = 0;
    const DEFAULT_LABEL = 'All tags';

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
            array_push($this->option, ['value' => self::DEFAULT_VALUE, 'label' => __(self::DEFAULT_LABEL)]);
            foreach ($tags as $tag) {
                array_push($this->option, ['value' => $tag->getId(), 'label' => __($tag->getName())]);
            }
        }
        return $this->option;
    }
}
