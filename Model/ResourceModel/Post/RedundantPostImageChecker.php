<?php

namespace Boolfly\Blog\Model\ResourceModel\Post;

class RedundantPostImageChecker
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * RedundantPostImageChecker constructor.
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
