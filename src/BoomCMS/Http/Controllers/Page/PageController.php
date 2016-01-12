<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Site;
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

    public function add(Request $request, Site $site, Page $page)
    {
        $this->authorize('add', $page);

        if (!$request->input('noprompt') && $page->shouldPromptOnAddPage()) {
            return [
                'prompt' => view("{$this->viewPrefix}add", [
                    'page' => $page,
                ])->render(),
            ];
        } else {
            $parent = $page->getAddPageParent();
            $newPage = $this->dispatch(new CreatePage(auth()->user(), $parent));

            Event::fire(new PageWasCreated($newPage, $site, $page));

            return [
                'url' => (string) $newPage->url(),
                'id'  => $newPage->getId(),
            ];
        }
    }

    public function discard(Page $page)
    {
        $this->authorize('edit', $page);
        $page->deleteDrafts();
    }

    public function urls(Page $page)
    {
        $this->authorize('editUrls', $page);

        return view($this->viewPrefix.'urls', [
            'page' => $page,
            'urls' => $page->getUrls(),
        ]);
    }
}
