<?php

namespace BoomCMS\Page\History\Diff;

use BoomCMS\Contracts\Models\PageVersion;
use Illuminate\Support\Facades\Lang;

abstract class BaseChange
{
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
        return $this->getDescription();
    }

    /**
     * Returns a description of the change
     *
     * @return string
     */
    public function getDescription()
    {
        return Lang::get($this->getDescriptionKey(), $this->getDescriptionParams());
    }

    /**
     * Reutrns the lang key for this change's description
     *
     * @return string
     */
    public function getDescriptionKey()
    {
        $type = strtolower(str_replace('Change', '', class_basename($this)));

        return "boomcms::page.history.diff.$type";
    }

    /**
     * Returns parameters for Lang::get() to be used in the description of the change
     *
     * @return array
     */
    public function getDescriptionParams()
    {
        return [];
    }
}
