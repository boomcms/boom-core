<?php

namespace BoomCMS\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion as Version;
use Illuminate\Support\Facades\Lang;

class ChunkChange extends BaseChange
{
    /**
     * @var string
     */
    protected $defaultIcon = 'pencil';

    /**
     * @var array
     */
    protected $icons = [
        'text'      => 'font',
        'link'      => 'link',
        'linkset'   => 'link',
        'asset'     => 'paperclip',
        'library'   => 'book',
        'calendar'  => 'calendar',
        'timestamp' => 'clock-o',
        'slideshow' => 'picture-o',
        'feature'   => 'newspaper-o',
        'location'  => 'globe',
        'html'      => 'code',
    ];

    /**
     * The type of chunk that was edited.
     *
     * @var string
     */
    protected $type;

    /**
     * @param Version $new
     * @param Version $old
     */
    public function __construct(Version $new, Version $old)
    {
        parent::__construct($new, $old);

        $this->type = $this->new->getChunkType();
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return isset($this->icons[$this->type]) ? $this->icons[$this->type] : $this->defaultIcon;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return Lang::get("boomcms::page.diff.chunk.{$this->type}");
    }
}
