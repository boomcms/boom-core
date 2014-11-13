<?php

use \Boom\Link\Link as Link;

class Model_Chunk_Linkset_Link extends ORM
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
    ];

    protected $_table_name = 'chunk_linkset_links';

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
        return $this->title;
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
