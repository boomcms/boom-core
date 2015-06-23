<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page;
use BoomCMS\Core\Facades\Editor;
use BoomCMS\Core\Facades\Chunk;

class ChunkLoader
{
    protected $chunks;
    protected $page;

    public function __construct(Page\Page $page, array $chunks)
    {
        $this->chunks = $chunks;
        $this->page = $page;
    }

    public function getChunks()
    {
        foreach ($this->chunks as $type => $slotnames) {
            $model = ucfirst($type);
            $class = "\BoomCMS\Core\Chunk\\" . $model;

            $models = Chunk::find($type, $slotnames, $this->page->getCurrentVersion());
            $found = [];

            foreach ($models as $m) {
                if ($m) {
                    $found[] = $m->slotname;
                    $this->chunks[$type][$m->slotname] = new $class($this->page, $m->toArray(), $m->slotname, Editor::isEnabled());
                }
            }

            $not_found = array_diff($slotnames, $found);
            foreach ($not_found as $slotname) {
                $this->chunks[$type][$slotname] = new $class($this->page, [], $slotname, Editor::isEnabled());
            }
        }

        return $this->chunks;
    }
}