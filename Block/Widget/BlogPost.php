<?php

namespace Boolfly\Blog\Block\Widget;

use Boolfly\Blog\Block\AbstractBlock;
use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\Post;
use Boolfly\Blog\Model\ResourceModel\Post\Collection;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Widget\Block\BlockInterface;

class BlogPost extends AbstractBlock implements BlockInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * BlogPost constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param Registry $registry
     * @param PageConfig $pageConfig
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context  $context,
        Config            $config,
        Registry          $registry,
        PageConfig        $pageConfig,
        CollectionFactory $collectionFactory,
        array             $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $config, $registry, $pageConfig, $data);
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getPostList()
    {
        $postList = $this->collectionFactory->create();
        $postList->addFieldToFilter('is_active', Post::STATUS_ENABLED);
        if (!empty($this->getData('category_filter'))) {
            $categoryFilter = $this->getData('category_filter');
            if ($categoryFilter != 0) {
                $postList->joinCategoryColumn();
                $postList->addFieldToFilter('category_id', $categoryFilter);
            }
        }
        if (!empty($this->getData('tag_filter'))) {
            $tagFilter = $this->getData('tag_filter');
            if ($tagFilter != 0) {
                $postList->joinTagColumn();
                $postList->addFieldToFilter('tag_id', $tagFilter);
            }
        }
        $postList->addStoreFilter($this->_storeManager->getStore());
        if (!empty($this->getData('post_display'))) {
            $postList->setOrder('post_id');
            $postList->setPageSize((int)$this->getData('post_display'));
        }
        return $postList;
    }

    public function getContentHeading()
    {
        return $this->getData('blog_widget_title');
    }

    public function getContentDescription()
    {
        return $this->getData('blog_widget_description');
    }

    /**
     * Add data to the widget.
     *
     * Retains previous data in the widget.
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr)
    {
        // TODO: Implement addData() method.
    }

    /**
     * Overwrite data in the widget.
     *
     * Param $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value.
     * If $key is an array, it will overwrite all the data in the widget.
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function setData($key, $value = null)
    {
        // TODO: Implement setData() method.
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [Post::CACHE_TAG . '_' . 'widget_posts'];
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    protected function _toHtml()
    {
        if (!$this->config->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}