<?php

namespace BoomCMS\Core\Controllers;

use BoomCMS\Core\Facades\Chunk;
use BoomCMS\Core\Facades\Page as PageFacade;

use Illuminate\Support\Facades\View;

class Page extends Controller
{
    public function show()
    {
        $page = $this->request->route()->getParameter('page');
        $template = $page->getTemplate();

        $chunks = Chunk::load($page, $template->getChunks());

        View::share('chunks', $chunks);
		View::share('chunk', function($type, $slotname, $page = null) use ($chunks) {
			if ($page) {
				return Chunk::get($type, $slotname, $page);
			}

			return (isset($chunks[$type][$slotname])) ?
				$chunks[$type][$slotname] :
				Chunk::edit($type, $slotname);
		});

        return $template->getView();
    }

    public function children()
    {
        $pages = PageFacade::findByParent(PageFacade::findById($this->request->input('parent')));
        $return = [];

        foreach ($pages as $page) {
            $return[] = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'url' => $page->url()->getLocation(),
                'visible' => (int) $page->isVisible(),
                'has_children' => (int) $page->hasChildren(),
            ];
        }

        return $return;
    }
}
