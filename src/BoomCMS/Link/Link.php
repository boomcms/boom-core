<?php

namespace BoomCMS\Link;

use BoomCMS\Contracts\LinkableInterface;
use BoomCMS\Support\Helpers\URL;

abstract class Link implements LinkableInterface
{
    /**
     * @var array
     */
    protected $attrs;

    /**
     * Array of query string parameters in the link.
     *
     * @var null|mixed
     */
    protected $query;

    /**
     * @var string
     */
    protected $link;

    /**
     * @param string $link
     */
    public function __construct($link, array $attrs = [])
    {
        $this->link = $link;
        $this->attrs = $attrs;
    }

    public function __toString()
    {
        return (string) $this->url();
    }

    /**
     * @param int|string $link
     * @param array      $attrs
     *
     * @return Link
     */
    public static function factory($link, array $attrs = [])
    {
        return (is_numeric($link) || URL::isInternal($link)) ?
            new Internal($link, $attrs) : new External($link, $attrs);
    }

    /**
     * Alias of getFeatureImageId(), for backwards compatibility.
     *
     * @return int
     */
    public function getAssetId(): int
    {
        return $this->attrs['asset_id'] ?? 0;
    }

    /**
     * Returns an array of link attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attrs;
    }

    /**
     * @return int
     */
    public function getFeatureImageId(): int
    {
        return $this->attrs['asset_id'] ?? 0;
    }

    /**
     * Returns the hostname for the link target.
     *
     * @return string
     */
    public function getHostname()
    {
        return parse_url($this->url(), PHP_URL_HOST);
    }

    /**
     * Returns the path portion of the link target.
     *
     * @return string
     */
    public function getPath()
    {
        return parse_url($this->url(), PHP_URL_PATH);
    }

    /**
     * Returns a query string parameter for a given key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        $query = $this->getQuery();

        return isset($query[$key]) ? $query[$key] : null;
    }

    /**
     * Returns an array of query string parameters in the URL.
     *
     * @return array
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $string = parse_url($this->link, PHP_URL_QUERY);

            parse_str($string, $this->query);
        }

        return $this->query;
    }

    public function getTargetPageId(): int
    {
        return $this->attrs['target_page_id'] ?? 0;
    }

    /**
     * Returns the contents of the text attribute.
     *
     * This is different to getText() which, for internal links returns the page standfirst if no text attribute is set.
     *
     * @return string
     */
    public function getTextAttribute(): string
    {
        return $this->attrs['text'] ?? '';
    }

    public function getTitleAttribute(): string
    {
        return $this->attrs['title'] ?? '';
    }

    public function getText(): string
    {
        return $this->getTextAttribute();
    }

    abstract public function getTitle(): string;

    public function getUrl(): string
    {
        return $this->url();
    }

    public function hasFeatureImage(): bool
    {
        return !empty($this->getFeatureImageId());
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isInternal(): bool
    {
        return false;
    }

    /**
     * Whether the link is valid.
     *
     * @return bool
     */
    abstract public function isValid(): bool;

    /**
     * Whether the link is visible.
     *
     * External links will always be visible
     *
     * Internal links are visible if the linked page is visible
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return true;
    }

    abstract public function url();
}
