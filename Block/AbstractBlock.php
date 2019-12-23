<?php

namespace Boolfly\Blog\Block;

use Boolfly\Blog\Model\Config\Config;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Element\Template;

abstract class AbstractBlock extends Template implements IdentityInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * AbstractBlock constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param Registry $registry
     * @param PageConfig $pageConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        Registry $registry,
        PageConfig $pageConfig,
        array $data = []
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return mixed|null
     */
    public function getCurrentAuthor()
    {
        if (!empty($this->registry->registry('chosen_author'))) {
            $currentAuthor = $this->registry->registry('chosen_author');
            return $currentAuthor;
        } else {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    public function getCurrentCategory()
    {
        if (!empty($this->registry->registry('chosen_category'))) {
            $currentCategory = $this->registry->registry('chosen_category');
            return $currentCategory;
        } else {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    public function getCurrentTag()
    {
        if (!empty($this->registry->registry('chosen_tag'))) {
            $currentTag = $this->registry->registry('chosen_tag');
            return $currentTag;
        } else {
            return null;
        }
    }

    /**
     * @param $breadCrumbsBlock
     * @throws NoSuchEntityException
     */
    protected function addBlogBreadCrumb($breadCrumbsBlock)
    {
        $blogTitle = ($this->config->getTitle()) ?: 'Blog';
        $blogUrl =  '/' . ($this->config->getRouter()) ?: 'bf_blog';
        $breadCrumbsBlock->addCrumb(
            'blog_page',
            [
                'label' => $blogTitle,
                'title' => $blogTitle,
                'link'  => $blogUrl
            ]
        );
    }

    /**
     * @param $breadCrumbsBlock
     * @throws NoSuchEntityException
     */
    protected function addHomeBreadCrumb($breadCrumbsBlock)
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $breadCrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $baseUrl
            ]
        );
    }
}
