<?php

namespace BoomCMS\Core\Model\Chunk\Linkset;

use Illuminate\Database\Eloquent\Model;
use BoomCMS\Core\Link\Link as Link;

class Link extends Model
{
    protected $_link;

    protected $_belongs_to = [
        'target'    => ['model' => 'page', 'foreign_key' => 'target_page_id']
    ];

    protected $_table_columns = [
        'id'                =>    '',
        'target_page_id'    =>    '',
        'chunk_linkset_id'    =>    '',
        'url'                =>    '',
        'title'                =>    '',
        'asset_id' => '',
    ];

    protected $table = 'chunk_linkset_links';

    public function getLink()
    {
        if ($this->_link === null) {
            $url = $this->target_page_id > 0 ? $this->target_page_id : $this->url;
            $this->_link = Link::factory($url);
        }

        return $this->_link;
    }

    public function getTitle()
    {
        return $this->title ?: $this->getLink()->getTitle();
    }

    public function isInternal()
    {
        return $this->getLink()->isInternal();
    }

    public function isExternal()
    {
        return ! $this->isInternal();
    }
}
