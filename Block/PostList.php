<?php

namespace Boolfly\Blog\Block;

use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\Post;
use Boolfly\Blog\Model\ResourceModel\Post\Collection;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class PostList extends AbstractBlock
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_PAGE_SIZE = 1;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * PostList constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param Registry $registry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $config, $registry, $pageConfig, $data);
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getPostList()
    {
        $configPageSize = $this->config->getPostsPerPage() ?: self::DEFAULT_PAGE_SIZE;
        $postList = $this->collectionFactory->create();
        $page=($this->getRequest()->getParam('p')) ?: self::DEFAULT_PAGE;
        $pageSize=($this->getRequest()->getParam('limit')) ?: $configPageSize;
        $postList->addFieldToFilter('is_active', Post::STATUS_ENABLED);
        $postList->addStoreFilter($this->_storeManager->getStore());

        if ($this->getCurrentCategory()) {
            $currentCategory = $this->getCurrentCategory();
            $postList->joinCategoryColumn();
            $postList->addFieldToFilter('category_id', (int)$currentCategory->getId());
        }

        if ($this->getCurrentTag()) {
            $currentTag = $this->getCurrentTag();
            $postList->joinTagColumn();
            $postList->addFieldToFilter('tag_id', (int)$currentTag->getId());
        }

        if ($this->getCurrentAuthor()) {
            $currentAuthor = $this->getCurrentAuthor();
            $postList->addFieldToFilter('author_id', (int)$currentAuthor->getId());
        }

        $postList->setOrder('post_id');
        $postList->setPageSize($pageSize);
        $postList->setCurPage($page);

        return $postList;
    }

    /**
     * Add Breadcrumbs for block
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function addBreadcrumbs()
    {
        $breadCrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $blogTitle = ($this->config->getTitle()) ?: 'Blog';

        if ($breadCrumbsBlock) {
            $this->addHomeBreadCrumb($breadCrumbsBlock);

            if ($currentCategory = $this->getCurrentCategory()) {
                $currentCategoryName = $currentCategory->getName();
                $this->addBlogBreadCrumb($breadCrumbsBlock);
                $breadCrumbsBlock->addCrumb(
                    'category_page',
                    [
                        'label' => $currentCategoryName,
                        'title' => $currentCategoryName,
                        'link'  => ''
                    ]
                );
            } elseif ($currentAuthor = $this->getCurrentAuthor()) {
                $currentAuthorName = $currentAuthor->getAuthorFullName();
                $this->addBlogBreadCrumb($breadCrumbsBlock);
                $breadCrumbsBlock->addCrumb(
                    'author_page',
                    [
                        'label' => $currentAuthorName,
                        'title' => $currentAuthorName,
                        'link'  => ''
                    ]
                );
            } elseif ($currentTag = $this->getCurrentTag()) {
                $currentTagName = $currentTag->getName();
                $this->addBlogBreadCrumb($breadCrumbsBlock);
                $breadCrumbsBlock->addCrumb(
                    'tag_page',
                    [
                        'label' => $currentTagName,
                        'title' => $currentTagName,
                        'link'  => ''
                    ]
                );
            } else {
                $breadCrumbsBlock->addCrumb(
                    'blog_page',
                    [
                        'label' => $blogTitle,
                        'title' => $blogTitle,
                        'link'  => ''
                    ]
                );
            }
        }
    }

    /**
     * @return $this|AbstractBlock
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        $title = $this->config->getTitle() ?: 'Blog';
        $configLimit = $this->config->getPostsPerPage() ?: self::DEFAULT_PAGE_SIZE;
        $metaDescription = ($this->config->getMetaDescription()) ?: 'Blog by Boolfly team';
        $metaTitle = ($this->config->getMetaTitle()) ?: 'Blog';
        $metaKeywords =($this->config->getMetaKeyWords()) ?:'Blog';
        $this->addBreadcrumbs();

        if ($this->getCurrentCategory()) {
            $currentCategory = $this->getCurrentCategory();
            $this->setPageConfig(
                ($currentCategory->getMetaTitle()) ?: $currentCategory->getName(),
                ($currentCategory->getMetaDescription()) ?: $metaDescription,
                ($currentCategory->getMetaKeyWords()) ?: $metaKeywords,
                ($currentCategory->getMetaTitle()) ?: $currentCategory->getName()
            );
            $pageMainTitle->setPageTitle($currentCategory->getName());
        } elseif ($this->getCurrentTag()) {
            $title = 'Tagged with' . ' \'' . $this->getCurrentTag()->getName() . '\'';
            $this->setPageConfig($title, $metaDescription, $metaKeywords, $title);
        } elseif ($this->getCurrentAuthor()) {
            $currentAuthor = $this->getCurrentAuthor();
            $title = $currentAuthor->getAuthorFullName();
            $this->setPageConfig(
                $title,
                ($currentAuthor->getDescription()) ?: $metaDescription,
                $metaKeywords,
                $title
            );
            $pageMainTitle->setPageTitle(' ');
        } else {
            $this->setPageConfig($metaTitle, $metaDescription, $metaKeywords, $metaTitle);
            $pageMainTitle->setPageTitle($title);
        }

        if ($this->getPostList()) {
            $pager = $this->getLayout()->getBlock('blog.posts');
            if (!$pager) {
                $pager = $this->getLayout()
                    ->createBlock('Magento\Theme\Block\Html\Pager', 'blog.posts')
                    ->setAvailableLimit([$configLimit=>$configLimit])
                    ->setShowPerPage(true);
            }
            $pager->setCollection($this->getPostList());
            $this->setChild('pager', $pager);
            $this->getPostList()->load();
        }
        return $this;
    }

    /**
     * @param $metaTitle
     * @param $metaDescription
     * @param $metaKeyWords
     * @param $title
     */
    protected function setPageConfig($metaTitle, $metaDescription, $metaKeyWords, $title)
    {
        $this->pageConfig->getTitle()->set($title);
        $this->pageConfig->setMetaTitle($metaTitle);
        $this->pageConfig->setDescription($metaDescription);
        $this->pageConfig->setKeywords($metaKeyWords);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return  [Post::CACHE_TAG . '_all_posts'];
    }
}
