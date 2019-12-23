<?php

namespace Boolfly\Blog\Model\Config\Source;

use Boolfly\Blog\Model\ResourceModel\Author\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Authors implements OptionSourceInterface
{
    /**
     * @var
     */
    protected $option;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Authors constructor.
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
            $authors = $this->collectionFactory->create();
            $this->option = [];
            foreach ($authors as $author) {
                array_push(
                    $this->option,
                    [
                        'value' => $author->getId(),
                        'label' => __($author->getFirstName() . ' ' . $author->getLastName())
                    ]
                );
            }
        }
        return $this->option;
    }
}
