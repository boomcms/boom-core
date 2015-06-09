<?php

namespace BoomCMS\Core\Controllers;

use BoomCMS\Core\Chunk\ChunkLoader;
use Illuminate\Support\Facades\View;

class Page extends Controller
{
    public function show()
    {
        $page = $this->request->route()->getParameter('boomcms.currentPage');
        $template = $page->getTemplate();

        $loader = new ChunkLoader($page, $template->getChunks());

        View::share('chunks', $loader->getChunks());
        return $template->getView();
    }
}
