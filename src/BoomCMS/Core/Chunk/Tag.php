<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\View;

class Tag extends BaseChunk
{
    protected $_default_template = 'gallery';
    protected $tag;
    protected $type = 'tag';

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        $this->tag = isset($attrs['tag']) ? $attrs['tag'] : '';
    }

    protected function show()
    {
        return View::make($this->viewPrefix."tag.$this->template", [
            'tag' => $this->getTag(),
        ]);
    }

    protected function showDefault()
    {
        return View::make($this->viewPrefix."default.tag.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix . 'tag' => $this->getTag(),
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
