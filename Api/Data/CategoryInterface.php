<?php

namespace Boolfly\Blog\Api\Data;

interface CategoryInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CATEGORY_ID   = 'category_id';
    const NAME = 'name';
    const URL_KEY = 'url_key';
    const IS_ACTIVE = 'is_active';
    const META_TITLE ='meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get URL Key
     *
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Check Is Active
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Get Meta Title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get Meta Keywords
     *
     * @return string|null
     */
    public function getMetaKeyWords();

    /**
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get Update Time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set URL Key
     *
     * @param string $url
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * Set IsActive Value
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Set Meta Title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set Meta Keywords
     *
     * @param string $metaKeyWords
     * @return $this
     */
    public function setMetaKeywords($metaKeyWords);

    /**
     * Set Meta Description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);
}
