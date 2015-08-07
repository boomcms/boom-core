<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Commands\DeletePage;
use BoomCMS\Core\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\View;

class Delete extends PageController
{
    public function __construct(Page\Provider $provider, Auth $auth, Request $request)
    {
        parent::__construct($provider, $auth, $request);

        if (!($this->page->wasCreatedBy($this->auth->getPerson()) ||
                $this->auth->loggedIn('delete_page', $this->page) ||
                $this->auth->loggedIn('manage_pages')
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

        Bus::dispatch(new DeletePage($this->provider, $this->page));

        return $parentUrl;
    }
}
