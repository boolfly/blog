<?php

namespace Boolfly\Blog\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const BLOG_ENABLED = 'blog_setting/general/enable';
    const BLOG_TITLE = 'blog_setting/general/title';
    const BLOG_ROUTER = 'blog_setting/general/router';
    const BLOG_POSTS_PER_PAGE = 'blog_setting/general/posts_per_page';
    const BLOG_RELATED_POSTS = 'blog_setting/general/related_posts';
    const BLOG_META_DESCRIPTION = 'blog_setting/seo/meta_description';
    const BLOG_META_TITLE = 'blog_setting/seo/meta_title';
    const BLOG_META_KEYWORDS = 'blog_setting/seo/meta_keywords';
    const BLOG_NUMBER_TAGS_DISPLAY = 'blog_setting/sidebar/number_popular_tags';
    const BLOG_NUMBER_CATEGORIES_DISPLAY = 'blog_setting/sidebar/maximum_categories';
    const BLOG_NUMBER_RELATED_PRODUCTS_DISPLAY = 'blog_setting/related_products/number_related_products';
    const BLOG_RELATED_PRODUCTS_ENABLED = 'blog_setting/related_products/display_products';

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::BLOG_ENABLED, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(self::BLOG_TITLE, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(self::BLOG_META_DESCRIPTION, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(self::BLOG_META_TITLE, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMetaKeyWords()
    {
        return $this->scopeConfig->getValue(self::BLOG_META_KEYWORDS, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getRouter()
    {
        return $this->scopeConfig->getValue(self::BLOG_ROUTER, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getPostsPerPage()
    {
        return $this->scopeConfig->getValue(self::BLOG_POSTS_PER_PAGE, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getRelatedPosts()
    {
        return $this->scopeConfig->getValue(self::BLOG_RELATED_POSTS, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getNumberTags()
    {
        return $this->scopeConfig->getValue(self::BLOG_NUMBER_TAGS_DISPLAY, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getNumberCategories()
    {
        return $this->scopeConfig->getValue(self::BLOG_NUMBER_CATEGORIES_DISPLAY, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getNumberRelatedProducts()
    {
        return $this->scopeConfig->getValue(self::BLOG_NUMBER_RELATED_PRODUCTS_DISPLAY, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if (!$this->storeId) {
            $this->storeId = $this->storeManager->getStore()->getStoreId();
        }
        return $this->storeId;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabledRelatedProducts()
    {
        return $this->scopeConfig->isSetFlag(self::BLOG_RELATED_PRODUCTS_ENABLED, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }
}
