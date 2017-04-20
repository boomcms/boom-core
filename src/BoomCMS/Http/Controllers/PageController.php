<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Page\RssFeed;
use BoomCMS\Support\Facades\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show(Request $request)
    {
        $page = Router::getActivePage();
        $format = $request->format();

        if ($request->route() && $request->route()->getParameter('format')) {
            $format = $request->route()->getParameter('format');
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

        return $template->getView();
    }

    public function asRss(Page $page)
    {
        return new RssFeed($page);
    }
}
