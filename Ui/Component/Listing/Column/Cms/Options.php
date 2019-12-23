<?php

namespace Boolfly\Blog\Ui\Component\Listing\Column\Cms;

use Magento\Store\Model\Store;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class Options extends StoreOptions
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = Store::DEFAULT_STORE_ID;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
