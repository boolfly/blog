<?php

namespace Boolfly\Blog\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ImageUploader extends \Magento\Catalog\Model\ImageUploader
{
    /**
     * Uploader factory
     *
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * List of allowed image mime types
     *
     * @var string[]
     */
    private $allowedMimeTypes;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var Random
     */
    private $random;

    /**
     * ImageUploader constructor.
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param DateTime $date
     * @param Random $random
     * @param LoggerInterface $logger
     * @param $baseTmpPath
     * @param $basePath
     * @param $allowedExtensions
     * @param array $allowedMimeTypes
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        DateTime $date,
        Random $random,
        LoggerInterface $logger,
        $baseTmpPath,
        $basePath,
        $allowedExtensions,
        $allowedMimeTypes = []
    ) {
        parent::__construct(
            $coreFileStorageDatabase,
            $filesystem,
            $uploaderFactory,
            $storeManager,
            $logger,
            $baseTmpPath,
            $basePath,
            $allowedExtensions,
            $allowedMimeTypes
        );
        $this->uploaderFactory = $uploaderFactory;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->date = $date;
        $this->random = $random;
    }

    /**
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $gmtDate = $this->date->gmtDate('Y-m-d-H-i-s');
        $randomString = $this->random->getRandomString(5);

        /** @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        if (!$uploader->checkMimeType($this->allowedMimeTypes)) {
            throw new LocalizedException(__('File validation failed.'));
        }
        $newFileName = $gmtDate.'-'.$randomString.'.'.$uploader->getFileExtension();
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath), $newFileName);
        unset($result['path']);

        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        /**
         * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
         */
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }
}
