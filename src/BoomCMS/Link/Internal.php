<?php

namespace BoomCMS\Link;

abstract class Internal extends Link
{
    abstract protected function getContentFeatureImageId(): int;
    abstract protected function getContentText(): string;
    abstract protected function getContentTitle(): string;

    public function getFeatureImageId(): int
    {
        if (isset($this->attrs['asset_id']) && !empty($this->attrs['asset_id'])) {
            return (int) $this->attrs['asset_id'];
        }

        return $this->getContentFeatureImageId();
    }

    public function getTitle(): string
    {
        if (isset($this->attrs['title']) && !empty($this->attrs['title'])) {
            return $this->attrs['title'];
        }

        return $this->getContentTitle();
    }

    public function getText(): string
    {
        if (isset($this->attrs['text']) && !empty($this->attrs['text'])) {
            return $this->attrs['text'];
        }

        return $this->getContentText();
    }

    /**
     * @return bool
     */
    public function isInternal(): bool
    {
        return true;
    }
}
