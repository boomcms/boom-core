<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\ChunkWasCreated;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use BoomCMS\Support\Facades\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class Chunk extends PageController
{
    public function getEdit(Request $request, Page $page)
    {
        $this->authorize('editContent', $page);

        $type = $request->input('type');
        $chunk = ChunkFacade::get($type, $request->input('slotname'), $page);

        return view('boomcms::editor.chunk.'.$type, [
            'chunk' => $chunk,
        ]);
    }

    public function postSave(Request $request, Page $page)
    {
        $this->authorize('editContent', $page);

        $input = $request->input();

        if (isset($input['template'])) {
            $template = $input['template'];
            unset($input['template']);
        }

        $chunk = ChunkFacade::create($page, $input);

        if (isset($template)) {
            $chunk->template($template);
        }

        Router::setActivePage($page);
        View::share('page', $page);

        Event::fire(new ChunkWasCreated($page, $chunk));

        return [
            'status' => $page->getCurrentVersion()->getStatus(),
            'html'   => $chunk->render(),
        ];
    }
}
