<?php

namespace Boolfly\Blog\Api\Data;

interface AuthorInterface
{
    const AUTHOR_ID = 'author_id';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const IMAGE = 'image';
    const URL_KEY = 'url_key';
    const DESCRIPTION = 'description';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get FirstName
     *
     * @return string|null
     */
    public function getFirstName();

    /**
     * Get LastName
     *
     * @return string|null
     */
    public function getLastName();

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get URL Key
     *
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set FirstName
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * Set LastName
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Set URL Key
     *
     * @param string $url
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);
}
