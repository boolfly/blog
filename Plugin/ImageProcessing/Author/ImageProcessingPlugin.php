<?php

namespace Boolfly\Blog\Plugin\ImageProcessing\Author;

use Boolfly\Blog\Model\Author\File;
use Boolfly\Blog\Model\ImageUploader;
use Boolfly\Blog\Model\ResourceModel\Author\RedundantAuthorImageChecker;
use Boolfly\Blog\Plugin\ImageProcessing\AbstractImageProcessingPlugin;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Boolfly\Blog\Model\ResourceModel\Author as AuthorResource;
use Magento\Framework\Model\AbstractModel;

class ImageProcessingPlugin extends AbstractImageProcessingPlugin
{
    /**
     * @var RedundantAuthorImageChecker
     */
    private $redundantAuthorImageChecker;

    /**
     * @var File
     */
    private $file;

    /**
     * ImageProcessingPlugin constructor.
     * @param File $file
     * @param ImageUploader $imageUploader
     * @param LoggerInterface $logger
     * @param RedundantAuthorImageChecker $redundantAuthorImageChecker
     */
    public function __construct(
        File $file,
        ImageUploader $imageUploader,
        LoggerInterface $logger,
        RedundantAuthorImageChecker $redundantAuthorImageChecker
    ) {
        parent::__construct($imageUploader, $logger);
        $this->file = $file;
        $this->redundantAuthorImageChecker = $redundantAuthorImageChecker;
    }

    /**
     * @param AuthorResource $subject
     * @param AbstractModel $author
     */
    public function beforeSave(AuthorResource $subject, AbstractModel $author)
    {
        $value = $author->getData('image');
        if ($imageName = $this->getUploadedImageName($value)) {
            $author->setData('image', $value[0]['name']);
            $author->setData('image_obj', $value);
        } elseif (!is_string($value)) {
            $author->setData('image', null);
        }
    }

    /**
     * @param AuthorResource $subject
     * @param $result
     * @param AbstractModel $author
     * @return AuthorResource
     */
    public function afterSave(AuthorResource $subject, $result, AbstractModel $author): AuthorResource
    {
        $value = $author->getData('image_obj');
        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            try {
                $this->imageUploader->moveFileFromTmp($imageName);
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
            }
        }

        //Remove Image
        $originalImage = $author->getOrigData('image');
        if (null !== $originalImage
            && $originalImage !== $author->getData('image')
            && $this->redundantAuthorImageChecker->execute($originalImage)
        ) {
            $this->file->delete($originalImage);
        }
        return $result;
    }

    /**
     * @param AuthorResource $subject
     * @param $result
     * @param AbstractModel $author
     * @return AuthorResource
     */
    public function afterDelete(AuthorResource $subject, $result, AbstractModel $author): AuthorResource
    {
        $image = $author->getData('image');
        if ($image) {
            $this->file->delete($image);
        }
        return $result;
    }
}
