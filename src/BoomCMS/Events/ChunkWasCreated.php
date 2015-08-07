<?php

namespace BoomCMS\Events;

use BoomCMS\Core\Chunk\BaseChunk as Chunk;
use BoomCMS\Core\Page\Page;

class ChunkWasCreated extends AbstractPageEvent
{
    /**
     * @var Chunk 
     */
    protected $chunk;

    public function __construct(Page $page, Chunk $chunk)
    {
        parent::__construct($page);
        
        $this->chunk = $chunk;
    }
    
    public function getChunk()
    {
        return $this->chunk;
    }
}
