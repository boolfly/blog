<?php

namespace Boolfly\Blog\Model;

use Boolfly\Blog\Api\Data\AuthorInterface;
use Boolfly\Blog\Model\Author\File;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Author extends AbstractModel implements IdentityInterface, AuthorInterface
{
    const CACHE_TAG = 'bf_blog_author';

    protected $_cacheTag = 'bf_blog_author';

    protected $_eventObject = 'bf_blog_author';

    protected $_eventPrefix = 'bf_blog_author';

    protected $storeManager;

    private $file;

    public function __construct(
        Context $context,
        Registry $registry,
        File $file,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->file = $file;
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('Boolfly\Blog\Model\ResourceModel\Author');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * Get FirstName
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    /**
     * Get LastName
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    public function getFullName()
    {
        $fullName = $this->getFirstName() . ' ' . $this->getLastName();
        return $fullName;
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get URL Key
     *
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set FirstName
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * Set LastName
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set URL Key
     *
     * @param string $url
     * @return $this
     */
    public function setUrlKey($url)
    {
        return $this->setData(self::URL_KEY, $url);
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Returns image url
     *
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $store = $this->storeManager->getStore();

                $isRelativeUrl = substr($image, 0, 1) === '/';

                $mediaBaseUrl = $store->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                );

                if ($isRelativeUrl) {
                    $url = $image;
                } else {
                    $url = $mediaBaseUrl
                        . ltrim(File::ENTITY_MEDIA_PATH, '/')
                        . '/'
                        . $image;
                }
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * @param $url_key
     * @return mixed
     * @throws LocalizedException
     */
    public function checkUrlKey($url_key)
    {
        return $this->_getResource()->checkUrlKey($url_key);
    }

    /**
     * @return string
     */
    public function getAuthorFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
