<?php

namespace Boolfly\Blog\Ui\DataProvider;

use Boolfly\Blog\Model\Post;
use Boolfly\Blog\Model\Post\File;
use Boolfly\Blog\Model\ResourceModel\Post\Collection;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

class PostDataProvider extends AbstractDataProvider
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var File
     */
    private $fileInfo;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Data
     */
    protected $pricingHelper;

    /**
     * PostDataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param Registry $coreRegistry
     * @param File $fileInfo
     * @param CollectionFactory $collectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Data $pricingHelper
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Registry $coreRegistry,
        File $fileInfo,
        CollectionFactory $collectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        Data $pricingHelper,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->coreRegistry = $coreRegistry;
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->fileInfo = $fileInfo;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->pricingHelper = $pricingHelper;
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

        $currentPost = $this->coreRegistry->registry('current_post');
        if ($currentPost->getId()) {
            $this->prepareProductData($currentPost);
            $postData = $currentPost->getData();
            $postData = $this->convertValues($currentPost, $postData);
            $this->loadedData[$currentPost->getId()] = $postData;
        } else {
            $this->loadedData = [];
        }
        $data = $this->dataPersistor->get('blog_post');
        if (!empty($data)) {
            $post = $this->collection->getNewEmptyItem();
            $post->setData($data);
            $this->loadedData[$post->getId()] = $post->getData();
            $this->dataPersistor->clear('blog_post');
        }

        return $this->loadedData;
    }

    /**
     * Converts brand image data to acceptable for rendering format
     *
     * @param $post
     * @param $postData
     * @return array
     * @throws FileSystemException
     */
    private function convertValues($post, $postData)
    {
        $fileName = $post->getData('image');
        $fileInfo = $this->getFileInfo();
        if ($fileName && $fileInfo->isFile($fileName)) {
            $stat = $fileInfo->getStat($fileName);
            $mime = $fileInfo->getMimeType($fileName);
            unset($postData['image']);
            $postData['image'][0]['name'] = basename($fileName);
            $postData['image'][0]['url'] = $post->getImageUrl();
            $postData['image'][0]['size'] = isset($stat) ? $stat['size'] : 0;
            $postData['image'][0]['type'] = $mime;
        } else {
            $postData['image'] = null;
        }
        return $postData;
    }

    /**
     * @return File
     */
    private function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Prepare Product Data
     *
     * @param Post $post
     */
    private function prepareProductData($post)
    {
        $productIds = $post->getData('product_ids');
        if (is_array($productIds) && !empty($productIds)) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addIdFilter($productIds)->addAttributeToSelect('*');
            $newProductData = [];
            /** @var Product $product */
            foreach ($productCollection as $product) {
                $newProductData[] = [
                    'entity_id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $this->pricingHelper->currency($product->getPrice(), true, false),
                    'is_active' => (int)$product->getStatus(),
                    'sku' => $product->getSku()
                ];
            }
            $post->setData('products', $newProductData);
            $post->unsetData('product_ids');
        }
    }
}
