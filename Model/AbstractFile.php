<?php

namespace Boolfly\Blog\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;

abstract class AbstractFile
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = '/blog';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Mime
     */
    protected $mime;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var ReadInterface
     */
    protected $baseDirectory;

    /**
     * @var Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param Filesystem $filesystem
     * @param Filesystem\Driver\File $driverFile
     * @param DirectoryList $directoryList
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Filesystem\Driver\File $driverFile,
        DirectoryList $directoryList,
        Mime $mime
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
        $this->driverFile = $driverFile;
        $this->directoryList = $directoryList;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    protected function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * Get Base Directory read instance
     *
     * @return ReadInterface
     */
    protected function getBaseDirectory()
    {
        if (!isset($this->baseDirectory)) {
            $this->baseDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        }

        return $this->baseDirectory;
    }

    /**
     * Checks for whether $fileName string begins with media directory path
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    protected function isBeginsWithMediaDirectoryPath($fileName)
    {
        $filePath = ltrim($fileName, '/');
        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();

        return strpos($filePath, $mediaDirectoryRelativeSubpath) === 0;
    }

    /**
     * Get media directory subpath relative to base directory path
     *
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirectoryPathRelativeToBaseDirectoryPath()
    {
        $baseDirectoryPath = $this->getBaseDirectory()->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();

        return substr($mediaDirectoryPath, strlen($baseDirectoryPath));
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    public function getMimeType($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws FileSystemException
     */
    public function getStat($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        return $this->getMediaDirectory()->stat($filePath);
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isExist($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * @param $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isFile($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        return $this->getMediaDirectory()->isFile($filePath);
    }

    /**
     * @param $fileName
     * @throws FileSystemException
     */
    public function delete($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $this->getMediaDirectory()->delete($filePath);
    }

    /**
     * Construct and return file subpath based on filename relative to media directory
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    protected function getFilePath($fileName)
    {
        $filePath = ltrim($fileName, '/');

        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();
        $isFileNameBeginsWithMediaDirectoryPath = $this->isBeginsWithMediaDirectoryPath($fileName);

        // if the file is not using a relative path, it resides in the catalog/category media directory
        $fileIsInCategoryMediaDir = !$isFileNameBeginsWithMediaDirectoryPath;

        if ($fileIsInCategoryMediaDir) {
            $filePath = static::ENTITY_MEDIA_PATH . '/' . $filePath;
        } else {
            $filePath = substr($filePath, strlen($mediaDirectoryRelativeSubpath));
        }

        return $filePath;
    }
}
