<?php

namespace BoomCMS\Page\History\Diff;

use BoomCMS\Contracts\Models\PageVersion;
use Illuminate\Support\Facades\Lang;

abstract class BaseChange
{
    /**
     * @var array
     */
    protected $iconClass = '';

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
     * Returns the FontAwesome icon class to use.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->iconClass;
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
        $type = strtolower(preg_replace('|Change$|', '', class_basename($this)));

        return "boomcms::page.diff.$type";
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
}
