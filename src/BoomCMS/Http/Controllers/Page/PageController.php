<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
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

        /*
         * Although no longer needed in this class, many classes extend this one
         * which still assume that this property will be set.
         * 
         * This can be safely removed once all controllers are updated to use DI to get the page parameter.
         * 
         * @see https://github.com/boomcms/boom-core/issues/171
         */
        $this->page = $this->request->route()->getParameter('page');
    }

    public function postAdd(Site $site, Page $page)
    {
        $this->authorize('add', $page);

        $parent = $page->getAddPageParent();
        $newPage = $this->dispatch(new CreatePage(auth()->user(), $site, $parent));

        Event::fire(new PageWasCreated($newPage, $page));

        return [
            'url' => (string) $newPage->url(),
            'id'  => $newPage->getId(),
        ];
    }

    public function postDiscard(Page $page)
    {
        $this->authorize('edit', $page);
        $page->deleteDrafts();
    }
}
