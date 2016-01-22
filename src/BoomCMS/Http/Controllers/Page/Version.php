<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Template;
use Illuminate\Http\Request;
use \DateTime;

class Version extends Controller
{
    protected $viewPrefix = 'boomcms::editor.page.version';

    public function getEmbargo(Page $page)
    {
        $this->authorize('editContent', $page);

        return view("$this->viewPrefix.embargo", [
            'version' => $page->getCurrentVersion(),
        ]);
    }

    public function getStatus(Page $page)
    {
        $this->authorize('editContent', $page);

        return view("$this->viewPrefix.status", [
            'page'    => $page,
            'version' => $page->getCurrentVersion(),
            'auth'    => auth(),
        ]);
    }

    public function getTemplate(Page $page)
    {
        $this->authorize('editTemplate', $page);

        return view("$this->viewPrefix.template", [
            'current'     => $page->getTemplate(),
            'templates'   => Template::findValid(),
        ]);
    }

    public function requestApproval(Page $page)
    {
        $this->authorize('editContent', $page);

        $page->markUpdatesAsPendingApproval();

        Event::fire(new Events\PageApprovalRequested($page, auth()->user()));

        return $page->getCurrentVersion()->getStatus();
    }

    public function setEmbargo(Request $request, Page $page)
    {
        $this->authorize('editContent', $page);

        $embargoedUntil = new DateTime('@'.time());

        if ($time = $request->input('embargoed_until')) {
            $timestamp = strtotime($request->input('embargoed_until'));
            $embargoedUntil->setTimestamp($timestamp);
        }

        $this->page->setEmbargoTime($embargoedUntil);

        $version = $this->page->getCurrentVersion();

        if ($version->isPublished()) {
            Event::fire(new Events\PageWasPublished($page, auth()->user(), $version));
        } elseif ($version->isEmbargoed()) {
            Event::fire(new Events\PageWasEmbargoed($page, auth()->user(), $version));
        }

        return $this->page->getCurrentVersion()->getStatus();
    }
}
