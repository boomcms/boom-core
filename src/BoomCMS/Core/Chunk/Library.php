<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\View;

class Library extends BaseChunk
{
    protected $defaultTemplate = 'gallery';
    protected $tag;

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        $this->tag = isset($attrs['tag']) ? $attrs['tag'] : '';
    }

    protected function show()
    {
        return View::make($this->viewPrefix."library.$this->template", [
            'tag' => $this->getTag(),
        ]);
    }

    protected function showDefault()
    {
        return View::make($this->viewPrefix."default.library.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'tag' => $this->getTag(),
        ];
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function hasContent()
    {
        return $this->getTag() != null;
    }
}
