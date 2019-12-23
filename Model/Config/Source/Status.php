<?php

namespace Boolfly\Blog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

class Status implements OptionSourceInterface
{
    /**
     * @var
     */
    protected $option;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if (!$this->option) {
            $this->option = [
                [
                    'value' => ProductStatus::STATUS_ENABLED,
                    'label' => __('Enabled')
                ],
                [
                    'value' => ProductStatus::STATUS_DISABLED,
                    'label' => __('Disabled')
                ]
            ];
        }
        return $this->option;
    }
}
