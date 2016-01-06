<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Core\Page\RssFeed;
use BoomCMS\Routing\Router;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show(Router $router)
    {
        $page = $router->getActivePage();
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

    public function asHtml(Page $page)
    {
        $template = $page->getTemplate();

        View::share('chunk', function ($type, $slotname, $page = null) {
            $chunks = [];

            if ($page) {
                return ChunkFacade::get($type, $slotname, $page);
            }

            return (isset($chunks[$type][$slotname])) ?
                $chunks[$type][$slotname] :
                ChunkFacade::edit($type, $slotname);
        });

        return $template->getView();
    }

    public function asRss(Page $page)
    {
        return new RssFeed($page);
    }
}
