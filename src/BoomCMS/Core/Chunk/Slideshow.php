<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Contracts\Models\Page;
use Illuminate\Support\Facades\View;

class Slideshow extends BaseChunk
{
    protected $defaultTemplate = 'circles';

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        if (isset($this->attrs['slides'])) {
            foreach ($this->attrs['slides'] as &$slide) {
                $slide = new Slideshow\Slide($slide);
            }
        }
    }

    protected function show()
    {
        return View::make($this->viewPrefix."slideshow/$this->template", [
            'title'  => $this->getTitle(),
            'slides' => $this->getSlides(),
        ])->render();
    }

    public function showDefault()
    {
        return View::make($this->viewPrefix."default.slideshow.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ])->render();
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
