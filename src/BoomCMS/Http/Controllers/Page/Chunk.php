<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\ChunkWasCreated;
use BoomCMS\Support\Facades\Chunk as ChunkFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class Chunk extends PageController
{
    public function __construct(Page $page)
    {
        $this->authorize('editContent', $page);
    }

    public function getEdit(Request $request, Page $page)
    {
        $type = $request->input('type');
        $chunk = ChunkFacade::get($type, $request->input('slotname'), $page);

        return view('boomcms::editor.chunk.'.$type, [
            'chunk' => $chunk,
        ]);
    }

    public function postSave(Request $request, Page $page)
    {
        $input = $request->input();

        if (isset($input['template'])) {
            $template = $input['template'];
            unset($input['template']);
        }

        $chunk = ChunkFacade::create($page, $input);

        if (isset($template)) {
            $chunk->template($template);
        }

        // This is usually defined by the page controller.
        // We need to define a variant of it incase the callback is used in teh chunk view.
        View::share('chunk', function ($type, $slotname, $page = null) {
            return ChunkFacade::get($type, $slotname, $page);
        });

        View::share('page', $page);

        Event::fire(new ChunkWasCreated($page, $chunk));

        return [
            'status' => $page->getCurrentVersion()->getStatus(),
            'html'   => $chunk->render(),
        ];
    }
}
