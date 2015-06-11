<?php

namespace BoomCMS\Core\Controllers;

use BoomCMS\Core\Chunk\ChunkLoader;
use BoomCMS\Core\Facades\Page as PageFacade;

use Illuminate\Support\Facades\View;

class Page extends Controller
{
    public function show()
    {
        $page = $this->editor->getActivePage();
        $template = $page->getTemplate();

        $loader = new ChunkLoader($page, $template->getChunks());

        View::share('chunks', $loader->getChunks());
        return $template->getView();
    }
    
    public function children()
    {
        $pages = PageFacade::findByParentId($this->request->input('parent'));
        $return = [];

        foreach ($pages as $page) {
            $return[] = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'url' => (string) $page->url(),
                'visible' => (int) $page->isVisible(),
                'has_children' => (int) $page->hasChildren(),
            ];
        }

        return $return;
    }
}
