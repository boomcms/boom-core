<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Support\Facades\View;

class Feature extends BaseChunk
{
    /**
     * @var Page\Page
     */
    protected $targetPage;

    public function __construct(Page $page, array $attrs, $slotname, $editable = true)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        if (isset($this->attrs['target_page_id'])) {
            $this->targetPage = PageFacade::find($this->attrs['target_page_id']);
        }
    }

    /**
     * Show a chunk where the target is set.
     */
    public function show()
    {
        $page = $this->getTargetPage();

        // Only show the page feature if the page is visible or the feature box is editable.
        if ($this->editable || $page->isVisible()) {
            return View::make($this->viewPrefix."feature.$this->template", [
                'target' => $page,
            ]);
        }
    }

    public function showDefault()
    {
        return View::make($this->viewPrefix."default.feature.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'target' => $this->target(),
        ];
    }

    public function hasContent()
    {
        return $this->targetPage !== null;
    }

    public function target()
    {
        return $this->targetPage ? $this->targetPage->getId() : 0;
    }

    /**
     * @return Page\Page
     */
    public function getTargetPage()
    {
        return $this->targetPage;
    }
}
