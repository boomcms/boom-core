<?php

namespace BoomCMS\Page\History\Diff;

use BoomCMS\Contracts\Models\PageVersion;
use Illuminate\Support\Facades\Lang;

abstract class BaseChange
{
    /**
     * Prefix to be used for Lang::get() keys.
     *
     * @var string
     */
    protected $langPrefix;

    /**
     * @var PageVersion
     */
    protected $new;

    /**
     * @var PageVersion
     */
    protected $old;

    /**
     * @param PageVersion $new
     * @param PageVersion $old
     */
    public function __construct(PageVersion $new, PageVersion $old)
    {
        $this->new = $new;
        $this->old = $old;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSummary();
    }

    /**
     * @return string
     */
    protected function getLangPrefix()
    {
        if ($this->langPrefix === null) {
            $type = strtolower(preg_replace('|Change$|', '', class_basename($this)));
            $this->langPrefix = "boomcms::page.diff.$type.";
        }

        return $this->langPrefix;
    }

    /**
     * Returns a summary of the change.
     *
     * @return string
     */
    public function getSummary()
    {
        return Lang::get($this->getSummaryKey(), $this->getSummaryParams());
    }

    /**
     * Reutrns the lang key for this change's summary.
     *
     * @return string
     */
    public function getSummaryKey()
    {
        return $this->getLangPrefix().'summary';
    }

    /**
     * Returns parameters for Lang::get() to be used in the summary of the change.
     *
     * @return array
     */
    public function getSummaryParams()
    {
        return [];
    }

    /**
     * Returns a description of the new version.
     *
     * @return string
     */
    public function getNewDescription()
    {
        return Lang::get($this->getNewDescriptionKey(), $this->getNewDescriptionParams());
    }

    /**
     * Reutrns the lang key for the description of the new version.
     *
     * @return string
     */
    public function getNewDescriptionKey()
    {
        return $this->getLangPrefix().'new';
    }

    /**
     * Returns parameters for Lang::get() to be used in the description of the new version.
     *
     * @return array
     */
    public function getNewDescriptionParams()
    {
        return [];
    }

    /**
     * Returns a description of the old version.
     *
     * @return string
     */
    public function getOldDescription()
    {
        return Lang::get($this->getOldDescriptionKey(), $this->getOldDescriptionParams());
    }

    /**
     * Reutrns the lang key for the description of the old version.
     *
     * @return string
     */
    public function getOldDescriptionKey()
    {
        return $this->getLangPrefix().'old';
    }

    /**
     * Returns parameters for Lang::get() to be used in the description of the old version.
     *
     * @return array
     */
    public function getOldDescriptionParams()
    {
        return [];
    }
}
