<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Jobs\DeletePage;
use BoomCMS\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\View;

class Delete extends PageController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        if (!($this->page->wasCreatedBy(Auth::getPerson()) ||
                Auth::loggedIn('delete_page', $this->page) ||
                Auth::loggedIn('manage_pages')
            ) || !$this->page->canBeDeleted()
        ) {
            abort(403);
        }
    }

    public function confirm()
    {
        return View::make($this->viewPrefix.'delete', [
            'count' => $this->page->countChildren(),
            'page'  => $this->page,
        ]);
    }

    public function delete()
    {
        $parentUrl = $this->page->getParent()->url();

        if ($this->request->input('with_children') == 1) {
            Bus::dispatch(new DeletePageChildren($this->page));
        }

        Bus::dispatch(new DeletePage($this->page));

        return $parentUrl;
    }
}
