<?php

namespace Boolfly\Blog\Model;

use Boolfly\Blog\Api\Data\PostInterface;
use Boolfly\Blog\Model\Post\File;
use Boolfly\Blog\Model\Config\Config;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Post extends AbstractModel implements IdentityInterface, PostInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const CACHE_TAG = 'bf_blog_post';

    protected $_cacheTag = 'bf_blog_post';

    protected $_eventObject = 'bf_blog_post';

    protected $_eventPrefix = 'bf_blog_post';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Config $config,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\ResourceModel\Post');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::POST_ID);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get URL Key
     *
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * Get Short Content
     *
     * @return string|null
     */
    public function getShortContent()
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Check Is Active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get Meta Title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * Get Meta Key Words
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * Get Author ID
     *
     * @return int|null
     */
    public function getAuthorId()
    {
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get Update Time
     *
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set URL Key
     *
     * @param string $url
     * @return $this
     */
    public function setUrlKey($url)
    {
        return $this->setData(self::URL_KEY, $url);
    }

    /**
     * Set Short Content
     *
     * @param string $shortContent
     * @return $this
     */
    public function setShortContent($shortContent)
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * Set Content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set IsActive Value
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Set Meta Title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * Set Meta Description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set Author ID
     *
     * @param int $authorId
     * @return $this
     */
    public function setAuthorId($authorId)
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    /**
     * @param $url_key
     * @return mixed
     * @throws LocalizedException
     */
    public function checkUrlKey($url_key)
    {
        return $this->_getResource()->checkUrlKeyAndGetId($url_key);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getAuthorUrl()
    {
        return $this->_getResource()->getAuthorUrlKey($this->getAuthorId());
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getAuthorFullName()
    {
        return $this->_getResource()->lookupAuthorFullName($this->getAuthorId());
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPostUrl()
    {
        $prefixUrl = $this->config->getRouter();
        $urlKey = trim($this->getUrlKey());
        $postUrl = (!empty($prefixUrl)) ? '/' . $prefixUrl . '/' . $urlKey : '/bf_blog' . '/' . $urlKey;
        return $postUrl;
    }

    /**
     * Set Meta Key Words
     *
     * @param $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Returns image url
     *
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $store = $this->storeManager->getStore();

                $isRelativeUrl = substr($image, 0, 1) === '/';

                $mediaBaseUrl = $store->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                );

                if ($isRelativeUrl) {
                    $url = $image;
                } else {
                    $url = $mediaBaseUrl
                        . ltrim(File::ENTITY_MEDIA_PATH, '/')
                        . '/'
                        . $image;
                }
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @param $productData
     * @return void
     */
    public function prepareProductData($productData)
    {
        $productIds = [];
        if (isset($productData) && is_array($productData)) {
            foreach ($productData as $value) {
                if (isset($value['entity_id'])) {
                    $productIds[] = $value['entity_id'];
                }
            }
        }
        $this->setData('product_ids', $productIds);
    }
}
