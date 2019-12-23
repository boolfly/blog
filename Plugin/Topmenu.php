<?php

namespace Boolfly\Blog\Plugin;

use Boolfly\Blog\Model\Config\Config;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Topmenu
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Topmenu constructor.
     * @param Config $config
     * @param NodeFactory $nodeFactory
     */
    public function __construct(Config $config, NodeFactory $nodeFactory)
    {
        $this->config = $config;
        $this->nodeFactory = $nodeFactory;
    }

    /**
     *
     * Inject node into menu.
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @throws NoSuchEntityException
     */
    public function beforeGetHtml(\Magento\Theme\Block\Html\Topmenu $subject)
    {
        $node = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $subject->getMenu()->addChild($node);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getNodeAsArray()
    {
        $url = '/' . $this->config->getRouter();
        $label = $this->config->getTitle();

        if (!$this->config->isEnabled()) {
            return null;
        }

        return [
            'name' => (!empty($label)) ? __($label) : __('Blog'),
            'id' => 'blog',
            'url' => (!empty($url)) ? $url : '/bf_blog'
        ];
    }
}
