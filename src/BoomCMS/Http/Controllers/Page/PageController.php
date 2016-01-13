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
        
        /**
         * Although no longer needed in this class, many classes extend this one
         * which still assume that this property will be set.
         * 
         * This can be safely removed once all controllers are updated to use DI to get the page parameter.
         * 
         * @see https://github.com/boomcms/boom-core/issues/171
         */
        $this->page = $this->request->route()->getParameter('page');
    }
    
    public function add(Request $request, Page $page)
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

            Event::fire(new PageWasCreated($newPage, $page));

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
