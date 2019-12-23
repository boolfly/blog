<?php

namespace Boolfly\Blog\Ui\DataProvider;

use Boolfly\Blog\Model\Author\File;
use Boolfly\Blog\Model\ResourceModel\Author\Collection;
use Boolfly\Blog\Model\ResourceModel\Author\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Ui\DataProvider\AbstractDataProvider;

class AuthorDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var File
     */
    private $fileInfo;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * CategoryDataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param File $fileInfo
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        File $fileInfo,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->fileInfo = $fileInfo;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $author \Boolfly\Blog\Model\Author */
        foreach ($items as $author) {
            $authorData = $author->getData();
            $authorData = $this->convertValues($author, $authorData);
            $this->loadedData[$author->getId()] = $authorData;
        }

        $data = $this->dataPersistor->get('blog_author');
        if (!empty($data)) {
            $author = $this->collection->getNewEmptyItem();
            $author->setData($data);
            $this->loadedData[$author->getId()] = $author->getData();
            $this->dataPersistor->clear('blog_author');
        }

        return $this->loadedData;
    }

    /**
     * Converts brand image data to acceptable for rendering format
     *
     * @param $author
     * @param $authorData
     * @return array
     * @throws FileSystemException
     */
    private function convertValues($author, $authorData)
    {
        $fileName = $author->getData('image');
        $fileInfo = $this->getFileInfo();
        if ($fileName && $fileInfo->isFile($fileName)) {
            $stat = $fileInfo->getStat($fileName);
            $mime = $fileInfo->getMimeType($fileName);
            unset($authorData['image']);
            $authorData['image'][0]['name'] = basename($fileName);
            $authorData['image'][0]['url'] = $author->getImageUrl();
            $authorData['image'][0]['size'] = isset($stat) ? $stat['size'] : 0;
            $authorData['image'][0]['type'] = $mime;
        } else {
            $authorData['image'] = null;
        }
        return $authorData;
    }

    /**
     * @return File
     */
    private function getFileInfo()
    {
        return $this->fileInfo;
    }
}
