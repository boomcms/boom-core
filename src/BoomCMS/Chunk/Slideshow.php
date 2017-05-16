<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use Illuminate\Support\Facades\View;

class Slideshow extends BaseChunk
{
    public function __construct(Page $page, array $attrs, $slotname)
    {
        parent::__construct($page, $attrs, $slotname);

        if (isset($this->attrs['slides'])) {
            foreach ($this->attrs['slides'] as $i => &$slide) {
                $slide = new Slideshow\Slide($slide);

                if ($slide->getAsset() === null) {
                    unset($this->attrs['slides'][$i]);
                }
            }
        }
    }

    protected function show()
    {
        return View::make($this->viewPrefix."slideshow/$this->template", [
            'title'  => $this->getTitle(),
            'slides' => $this->getSlides(),
        ]);
    }

    public function hasContent()
    {
        return count($this->getSlides()) > 0;
    }

    public function getSlides()
    {
        return isset($this->attrs['slides']) ? $this->attrs['slides'] : [];
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }
}
