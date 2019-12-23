<?php

namespace Boolfly\Blog\Model;

use Boolfly\Blog\Api\Data\CategoryInterface;
use Boolfly\Blog\Model\Config\Config;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Category extends AbstractModel implements IdentityInterface, CategoryInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const CACHE_TAG = 'bf_blog_category';

    protected $_cacheTag = 'bf_blog_category';

    protected $_eventObject = 'bf_blog_category';

    protected $_eventPrefix = 'bf_blog_category';

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Context $context,
        Registry $registry,
        Config $config,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\ResourceModel\Category');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
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
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCategoryUrl()
    {
        $prefixUrl = $this->config->getRouter();
        $urlKey = trim($this->getUrlKey());
        $postUrl = (!empty($prefixUrl)) ? '/' . $prefixUrl . '/' . $urlKey : '/bf_blog' . '/' . $urlKey;
        return $postUrl;
    }

    /**
     * Get Meta Keywords
     *
     * @return string|null
     */
    public function getMetaKeyWords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * Set Meta Keywords
     *
     * @param string $metaKeyWords
     * @return $this
     */
    public function setMetaKeywords($metaKeyWords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeyWords);
    }
}
