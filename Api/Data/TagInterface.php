<?php

namespace Boolfly\Blog\Api\Data;

interface TagInterface
{
    const TAG_ID = 'tag_id';
    const NAME = 'name';
    const URL_KEY = 'url_key';

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
}
