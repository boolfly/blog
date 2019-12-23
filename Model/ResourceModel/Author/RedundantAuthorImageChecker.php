<?php

namespace Boolfly\Blog\Model\ResourceModel\Author;

class RedundantAuthorImageChecker
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * RedundantAuthorImageChecker constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $imageName
     * @return bool
     */
    public function execute(string $imageName): bool
    {
        $authors = $this->collectionFactory->create()->addFieldToFilter('image', $imageName);
        return empty($authors->getSize());
    }
}
