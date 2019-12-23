<?php

namespace Boolfly\Blog\Plugin\ImageProcessing;

use Boolfly\Blog\Model\ImageUploader;
use Psr\Log\LoggerInterface;

abstract class AbstractImageProcessingPlugin
{
    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractImageProcessingPlugin constructor.
     * @param ImageUploader $imageUploader
     * @param LoggerInterface $logger
     */
    public function __construct(
        ImageUploader $imageUploader,
        LoggerInterface $logger
    ) {
        $this->imageUploader = $imageUploader;
        $this->logger = $logger;
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    protected function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    protected function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }
}
