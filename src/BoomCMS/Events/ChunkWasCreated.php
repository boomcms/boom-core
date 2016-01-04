<?php

namespace BoomCMS\Events;

use BoomCMS\Chunk\BaseChunk as Chunk;
use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Events\PageEvent;

class ChunkWasCreated extends PageEvent
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
