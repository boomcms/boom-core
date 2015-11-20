<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Events\ChunkWasCreated;
use BoomCMS\Http\Controllers\Controller;
use BoomcMS\Support\Facades\Auth;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class Chunk extends Controller
{
    /**
     * @var Page
     */
    protected $page;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = $this->request->route()->getParameter('page');

        $this->page->wasCreatedBy(Auth::getPerson()) ||
            parent::authorization('edit_page_content', $this->page);
    }

    public function getEdit()
    {
        $type = $this->request->input('type');
        $chunk = ChunkFacade::get($type, $this->request->input('slotname'), $this->page);

        return View::make('boomcms::editor.chunk.'.$type, [
            'chunk' => $chunk,
        ]);
    }

    public function postSave()
    {
        $input = $this->request->input();

        if (isset($input['template'])) {
            unset($input['template']);
        }

        $chunk = ChunkFacade::create($this->page, $input);

        if ($this->request->input('template')) {
            $chunk->template($this->request->input('template'));
        }

        // This is usually defined by the page controller.
        // We need to define a variant of it incase the callback is used in teh chunk view.
        View::share('chunk', function ($type, $slotname, $page = null) {
            return ChunkFacade::get($type, $slotname, $page);
        });

        View::share('page', $this->page);

        Event::fire(new ChunkWasCreated($this->page, $chunk));

        return [
            'status' => $this->page->getCurrentVersion()->getStatus(),
            'html'   => $chunk->render(),
        ];
    }
}
