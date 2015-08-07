<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Chunk\Provider;
use BoomCMS\Core\Page\Page;
use BoomCMS\Events\ChunkWasCreated;
use BoomCMS\Http\Controllers\Controller;
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

    /**
     * @var Provider
     */
    protected $provider;

    public function __construct(Auth $auth, Request $request, Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->page = $this->request->route()->getParameter('page');
        $this->provider = $provider;

        $this->page->wasCreatedBy($this->auth->getPerson()) ||
            parent::authorization('edit_page_content', $this->page);
    }

    public function getEdit()
    {
        $type = $this->request->input('type');
        $chunk = ChunkFacade::get($type, $this->request->input('slotname'), $this->page);

        return View::make('boom::editor.chunk.'.$type, [
            'chunk' => $chunk,
        ]);
    }

    public function postSave()
    {
        $input = $this->request->input();

        if (isset($input['template'])) {
            unset($input['template']);
        }

        $chunk = $this->provider->create($this->page, $input);

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
