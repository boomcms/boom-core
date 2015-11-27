<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Jobs\DeletePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\View;

class Delete extends PageController
{
    public function __construct(Auth $auth, Request $request)
    {
        parent::__construct($auth, $request);

        if (!($this->page->wasCreatedBy($auth->getPerson()) ||
                $auth->loggedIn('delete_page', $this->page) ||
                $auth->loggedIn('manage_pages')
            )
        ) {
            abort(403);
        }

        if (!$this->page->canBeDeleted()) {
            abort(423);
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
