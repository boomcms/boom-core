<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Core\Page;
use BoomCMS\Support\Facades\Chunk;
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

        $method = 'as'.ucfirst($format);

        if (method_exists($this, $method)) {
            return $this->$method($page);
        } else {
            abort(406);
        }
    }

    public function asHtml(Page\Page $page)
    {
        $template = $page->getTemplate();

        View::share('chunk', function ($type, $slotname, $page = null) {
            $chunks = [];

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
}
