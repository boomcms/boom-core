<?php

namespace BoomCMS\Core\Controllers;

use BoomCMS\Core\Page;
use BoomCMS\Core\Facades\Chunk;
use BoomCMS\Core\Facades\Page as PageFacade;

use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show()
    {
        $page = $this->request->route()->getParameter('page');
        $format = $this->request->format();

        if ($this->request->route()->getParameter('format')) {
            $format = $this->request->route()->getParameter('format');
        }

        $method = 'as' . ucfirst($format);

        if (method_exists($this, $method)) {
            return $this->$method($page);
        } else {
            abort(406);
        }
    }

    public function asHtml(Page\Page $page)
    {
        $template = $page->getTemplate();

        $chunks = Chunk::load($page, $template->getChunks());

        View::share('chunks', $chunks);
        View::share('chunk', function ($type, $slotname, $page = null) use ($chunks) {
            if ($page) {
                return Chunk::get($type, $slotname, $page);
            }

            return (isset($chunks[$type][$slotname])) ?
                $chunks[$type][$slotname] :
                Chunk::edit($type, $slotname);
        });

        return $template->getView();
    }

    public function asRss(Page\Page $page)
    {
        return new Page\RssFeed($page);
    }

    public function children()
    {
        $pages = PageFacade::findByParent(PageFacade::findById($this->request->input('parent')));
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
