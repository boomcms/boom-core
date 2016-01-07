<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Core\Page\RssFeed;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show()
    {
        $page = Router::getActivePage();
        $format = $this->request->format();

        if ($this->request->route()->getParameter('format')) {
            $format = $this->request->route()->getParameter('format');
        }

        $method = 'as'.ucfirst($format);

        if (method_exists($this, $method)) {
            return $this->$method($page);
        }
        
        abort(406);
        
    }

    public function asHtml(Page $page)
    {
        $template = $page->getTemplate();

        View::share('page', $page);
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
