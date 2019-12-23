<?php

namespace Boolfly\Blog\Block;

use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\Post;
use Boolfly\Blog\Model\PostFactory;
use Boolfly\Blog\Model\ResourceModel\Post\Collection as PostCollection;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Boolfly\Blog\Model\ResourceModel\Tag\Collection as TagCollection;
use Boolfly\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Zend_Db_Select;
use Zend_Filter_Exception;
use Zend_Filter_Interface;

class PostContent extends AbstractBlock
{
    const NUMBER_RELATED_PRODUCTS = 10;
    const NUMBER_RELATED_POSTS = 2;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var Zend_Filter_Interface
     */
    protected $templateProcessor;

    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * @var PostCollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var TagCollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var Status
     */
    protected $productStatus;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param PostFactory $postFactory
     * @param Zend_Filter_Interface $templateProcessor
     * @param Config $config
     * @param Registry $registry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param ImageBuilder $imageBuilder
     * @param ReviewRendererInterface $reviewRenderer
     * @param PostCollectionFactory $postCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param TagCollectionFactory $tagCollectionFactory
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PostFactory $postFactory,
        Zend_Filter_Interface $templateProcessor,
        Config $config,
        Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig,
        ImageBuilder $imageBuilder,
        ReviewRendererInterface $reviewRenderer,
        PostCollectionFactory $postCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        TagCollectionFactory $tagCollectionFactory,
        Status $productStatus,
        Visibility $productVisibility,
        array $data = []
    ) {
        $this->postFactory = $postFactory;
        $this->templateProcessor = $templateProcessor;
        $this->imageBuilder = $imageBuilder;
        $this->reviewRenderer = $reviewRenderer;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $config, $registry, $pageConfig, $data);
    }

    /**
     * @return Post|null
     */
    public function getPost()
    {
        if (!$this->registry->registry('chosen_post')) {
            $id = $this->getRequest()->getParam('id');
            return $this->postFactory->create()->load($id);
        }
        return $this->registry->registry('chosen_post');
    }

    /**
     * @return array|ProductCollection
     * @throws NoSuchEntityException
     */
    public function getRelatedProducts()
    {
        if ($this->config->isEnabledRelatedProducts()) {
            $maximumNumber = $this->config->getNumberRelatedProducts() ?: self::NUMBER_RELATED_PRODUCTS;
            $productIds = $this->getPost()->getProductIds();

            $relatedProducts = $this->productCollectionFactory->create();
            $relatedProducts->addIdFilter($productIds)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
                ->setVisibility($this->productVisibility->getVisibleInSiteIds())
                ->addStoreFilter()
                ->setPageSize($maximumNumber);
            return $relatedProducts;
        }
        return [];
    }

    /**
     * Get posts which are related to the current post
     *
     * @return array|PostCollection
     * @throws NoSuchEntityException
     */
    public function getRelatedPosts()
    {
        $currentPost = $this->getPost();
        $relatedPostIds = $currentPost->getRelatedPostIds();
        $relatedPosts = $this->postCollectionFactory->create();

        if (!count($relatedPostIds)) {
            return [];
        }

        $relatedPosts->addFieldToFilter('is_active', Post::STATUS_ENABLED)
            ->addStoreFilter($this->_storeManager->getStore());
        $relatedPosts->addFieldToFilter('post_id', ['in' => $relatedPostIds]);
        $relatedPosts->addFieldToFilter('post_id', ['neq' => $currentPost->getId()])
            ->setOrder('post_id')
            ->setPageSize($this->config->getRelatedPosts() ?: self::NUMBER_RELATED_POSTS);
        return $relatedPosts;
    }

    /**
     * Get recent posts
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getRecentPosts()
    {
        $currentPost = $this->getPost();
        $recentPosts = $this->postCollectionFactory->create();
        $recentPosts->addFieldToFilter('is_active', Post::STATUS_ENABLED)
            ->addStoreFilter($this->_storeManager->getStore());
        $recentPosts->addFieldToFilter('post_id', ['neq' => $currentPost->getId()])
            ->setOrder('post_id')
            ->setPageSize($this->config->getRelatedPosts() ?: self::NUMBER_RELATED_POSTS);

        return $recentPosts;
    }

    /**
     * Get the current post's tags
     * @return TagCollection
     */
    public function getTags()
    {
        $tags = $this->tagCollectionFactory->create();
        $tagIds = $this->getPost()->getData('tag_ids');
        $tags->addFieldToFilter('tag_id', ['in' => $tagIds]);
        return $tags;
    }

    /**
     * @param $string
     * @return mixed
     * @throws Zend_Filter_Exception
     */
    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return  $this->getPost()->getIdentities();
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->create($product, $imageId, $attributes);
    }

    /**
     * Return HTML block with price
     *
     * @param Product $product
     * @return string
     * @throws LocalizedException
     */
    public function getProductPrice(Product $product)
    {
        return $this->getProductPriceHtml(
            $product,
            FinalPrice::PRICE_CODE,
            Render::ZONE_ITEM_LIST
        );
    }

    /**
     * Return HTML block with tier price
     *
     * @param Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @throws LocalizedException
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Product $product
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * Check Product has URL
     *
     * @param Product $product
     * @return bool
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get product reviews summary
     *
     * @param Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->reviewRenderer->getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * @return $this|AbstractBlock
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        if ($post = $this->getPost()) {
            $this->addBreadcrumbs();
            $this->pageConfig->setKeywords($post->getMetaKeywords());
            $this->pageConfig->setDescription($post->getMetaDescription());
            $this->pageConfig->setMetaTitle($post->getMetaTitle());
            $this->pageConfig->getTitle()->set($post->getMetaTitle() ?: $post->getTitle());
        }
        return $this;
    }

    /**
     * Add Breadcrumbs for block
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function addBreadcrumbs()
    {
        if ($currentPost = $this->getPost()) {
            $breadCrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadCrumbsBlock) {
                $this->addHomeBreadCrumb($breadCrumbsBlock);
                $this->addBlogBreadCrumb($breadCrumbsBlock);
                $currentPostTitle = $currentPost->getTitle();
                $breadCrumbsBlock->addCrumb(
                    'post_page',
                    [
                        'label' => $currentPostTitle,
                        'title' => $currentPostTitle,
                        'link'  => ''
                    ]
                );
            }
        }
    }
}
