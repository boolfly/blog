<?php

namespace Boolfly\Blog\Block\Adminhtml\Edit;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends Template implements ButtonProviderInterface
{

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 20,
        ];
    }
}
