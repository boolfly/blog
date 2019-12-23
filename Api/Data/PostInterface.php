<?php

namespace Boolfly\Blog\Api\Data;

interface PostInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const POST_ID   = 'post_id';
    const TITLE = 'title';
    const URL_KEY = 'url_key';
    const SHORT_CONTENT = 'short_content';
    const CONTENT = 'content';
    const IS_ACTIVE = 'is_active';
    const META_TITLE ='meta_title';
    const META_DESCRIPTION = 'meta_description';
    const AUTHOR_ID = 'author_id';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const META_KEYWORDS = 'meta_keywords';
    const IMAGE = 'image';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get URL Key
     *
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Get Short Content
     *
     * @return string|null
     */
    public function getShortContent();

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent();

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
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get Meta Key Words
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get Author ID
     *
     * @return int|null
     */
    public function getAuthorId();

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
     * Get Image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set URL Key
     *
     * @param string $url
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * Set Short Content
     *
     * @param string $shortContent
     * @return $this
     */
    public function setShortContent($shortContent);

    /**
     * Set Content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

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
     * Set Meta Description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set Meta Key Words
     *
     * @param $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Set Author ID
     *
     * @param int $authorId
     * @return $this
     */
    public function setAuthorId($authorId);

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);
}
