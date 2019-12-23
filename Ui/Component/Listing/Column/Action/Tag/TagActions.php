<?php

namespace Boolfly\Blog\Ui\Component\Listing\Column\Action\Tag;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class TagActions extends Column
{
    const EDIT_URL = 'bf_blog/tag/edit';
    const DELETE_URL = 'bf_blog/tag/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * TagActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['tag_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::EDIT_URL,
                                [
                                    'tag_id' => $item['tag_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::DELETE_URL,
                                [
                                    'tag_id' => $item['tag_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Confirm deleting!'),
                                'message' => __('Are you sure want to delete this item?')
                            ]
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
