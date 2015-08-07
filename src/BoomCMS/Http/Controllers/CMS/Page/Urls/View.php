<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Urls;

use Illuminate\Support\Facades\View as ViewFacade;

class View extends BaseController
{
    public function add()
    {
        return ViewFacade::make("$this->viewPrefix/add", [
            'page' => $this->page,
        ]);
    }

    public function move()
    {
        return ViewFacade::make("$this->viewPrefix/move", [
            'url'     => $this->url,
            'current' => $this->url->getPage(),
            'page'    => $this->page,
        ]);
    }
}
