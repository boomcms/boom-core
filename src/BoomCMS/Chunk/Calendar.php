<?php

namespace BoomCMS\Chunk;

use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class Calendar extends BaseChunk
{
    /**
     * @return array
     */
    public function getDates()
    {
        return isset($this->attrs['content']) && !empty($this->attrs['content']) ? $this->attrs['content'] : [];
    }

    /**
     * @return View
     */
    protected function show()
    {
        return ViewFacade::make($this->viewPrefix."calendar.$this->template", [
            'dates' => $this->getDates(),
        ]);
    }

    /**
     * 
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->getDates());
    }
}
