<?php

namespace Boolfly\Blog\Block\Adminhtml\Edit;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(\Magento\Backend\Block\Widget\Context $context)
    {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->_getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * @return string
     */
    protected function _getBackUrl()
    {
        return $this->urlBuilder->getUrl('*/*/');
    }
}
