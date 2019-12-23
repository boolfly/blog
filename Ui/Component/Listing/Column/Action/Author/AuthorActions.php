<?php

namespace Boolfly\Blog\Ui\Component\Listing\Column\Action\Author;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class AuthorActions extends Column
{
    const EDIT_URL = 'bf_blog/author/edit';
    const DELETE_URL = 'bf_blog/author/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * CategoryActions constructor.
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
                if (isset($item['author_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::EDIT_URL,
                                [
                                    'author_id' => $item['author_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::DELETE_URL,
                                [
                                    'author_id' => $item['author_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Confirm deleting!'),
                                'message' => __('If you delete the author, all posts related with them will also be deleted.')
                            ]
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}

