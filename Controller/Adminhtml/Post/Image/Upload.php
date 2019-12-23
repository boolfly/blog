<?php

namespace Boolfly\Blog\Controller\Adminhtml\Post\Image;

use Boolfly\Blog\Controller\Adminhtml\AbstractPost;
use Boolfly\Blog\Model\ImageUploader;
use Boolfly\Blog\Model\PostFactory;
use Boolfly\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Exception;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class Upload extends AbstractPost implements HttpPostActionInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PostFactory $postFactory
     * @param LoggerInterface $logger
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param DataPersistorInterface $dataPersistor
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        PostFactory $postFactory,
        LoggerInterface $logger,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Filter $filter,
        DataPersistorInterface $dataPersistor,
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        parent::__construct(
            $context,
            $resultForwardFactory,
            $postFactory,
            $logger,
            $coreRegistry,
            $resultPageFactory,
            $collectionFactory,
            $filter,
            $dataPersistor
        );
    }

    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
