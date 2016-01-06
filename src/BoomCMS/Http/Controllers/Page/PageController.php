<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Events\PageWasCreated;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Jobs\CreatePage;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class PageController extends Controller
{
    use DispatchesCommands;

    protected $viewPrefix = 'boomcms::editor.page.';

    /**
     * @var Page
     */
    protected $page;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = $this->request->route()->getParameter('page');
    }

    public function add()
    {
        $this->authorize('add', $this->page);

        if ($this->page->shouldPromptOnAddPage()) {
            return [
                'prompt' => view("{$this->viewPreifx}add"),
            ];
        } else {
            $parent = $this->page->getAddPageParent();
            $newPage = $this->dispatch(new CreatePage($this->auth->user(), $parent));

            Event::fire(new PageWasCreated($newPage, $this->page));

            return [
                'url' => (string) $newPage->url(),
                'id'  => $newPage->getId(),
            ];
        }
    }

    public function discard()
    {
        $this->page->deleteDrafts();
    }

    public function urls()
    {
        return view($this->viewPrefix.'urls', [
            'page' => $this->page,
            'urls' => $this->page->getUrls(),
        ]);
    }
}
