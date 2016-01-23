<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Template;
use BoomCMS\Events;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class Version extends Controller
{
    protected $viewPrefix = 'boomcms::editor.page.version';

    /**
     * Show a form to set an embargo time
     *
     * @param Page $page
     *
     * @return View
     */
    public function getEmbargo(Page $page)
    {
        $this->authorize('publish', $page);

        return view("$this->viewPrefix.embargo", [
            'version' => $page->getCurrentVersion(),
        ]);
    }

    /**
     * Show the current status of the page
     *
     * @param Page $page
     *
     * @return View
     */
    public function getStatus(Page $page)
    {
        $this->authorize('editContent', $page);

        return view("$this->viewPrefix.status", [
            'page'    => $page,
            'version' => $page->getCurrentVersion(),
            'auth'    => auth(),
        ]);
    }

    /**
     * Show a form to change the template of the page
     *
     * @param Page $page
     *
     * @return View
     */
    public function getTemplate(Page $page)
    {
        $this->authorize('editTemplate', $page);

        return view("$this->viewPrefix.template", [
            'current'     => $page->getTemplate(),
            'templates'   => TemplateFacade::findValid(),
        ]);
    }

    /**
     * Mark the page as requiring approval
     *
     * @param Page $page
     *
     * @return string
     */
    public function requestApproval(Page $page)
    {
        $this->authorize('editContent', $page);

        $page->markUpdatesAsPendingApproval();

        Event::fire(new Events\PageApprovalRequested($page, auth()->user()));

        return $page->getCurrentVersion()->getStatus();
    }

    /**
     * Set an embargo time for the current drafts
     *
     * @param Request $request
     * @param Page $page
     *
     * @return string
     */
    public function setEmbargo(Request $request, Page $page)
    {
        $this->authorize('publish', $page);

        $embargoedUntil = new DateTime('@'.time());

        if ($time = $request->input('embargoed_until')) {
            $timestamp = strtotime($request->input('embargoed_until'));
            $embargoedUntil->setTimestamp($timestamp);
        }

        $page->setEmbargoTime($embargoedUntil);

        $version = $page->getCurrentVersion();

        if ($version->isPublished()) {
            Event::fire(new Events\PageWasPublished($page, auth()->user(), $version));
        } elseif ($version->isEmbargoed()) {
            Event::fire(new Events\PageWasEmbargoed($page, auth()->user(), $version));
        }

        return $version->getStatus();
    }

    /**
     * Set the template of the page
     *
     * @param Page $page
     * @param Template $template
     *
     * @return string
     */
    public function setTemplate(Page $page, Template $template)
    {
        $this->authorize('editTemplate', $page);

        $page->setTemplate($template);

        Event::fire(new Events\PageTemplateWasChanged($page, $template));

        return $page->getCurrentVersion()->getStatus();
    }

    /**
     * Set the title of the page
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array
     */
    public function setTitle(Request $request, Page $page)
    {
        $this->authorize('editContent', $page);

        $oldTitle = $page->getTitle();
        $page->setTitle($request->input('title'));

        Event::fire(new Events\PageTitleWasChanged($page, $oldTitle, $page->getTitle()));

        return [
            'status'   => $page->getCurrentVersion()->getStatus(),
            'location' => (string) $page->url(true),
        ];
    }
}
