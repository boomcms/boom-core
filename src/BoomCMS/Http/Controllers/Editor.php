<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Editor\Editor as EditorObject;
use BoomCMS\Page\History\Diff;
use BoomCMS\Support\Facades\Page as PageFacade;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Editor extends Controller
{
    public function postState(Request $request, EditorObject $editor)
    {
        $state = $request->input('state');
        $numericState = constant(EditorObject::class.'::'.strtoupper($state));

        $editor->setState($numericState);
    }

    public function postTime(Request $request, EditorObject $editor)
    {
        $timestamp = $request->input('time', time());
        $time = (new DateTime())->setTimestamp($timestamp);

        $editor
            ->setTime($time)
            ->setState(EditorObject::HISTORY);
    }

    /**
     * Displays the CMS interface with buttons for add page, settings, etc.
     * Called from an iframe when logged into the CMS.
     */
    public function getToolbar(EditorObject $editor, Request $request)
    {
        $page = PageFacade::find($request->input('page_id'));

        View::share([
            'page'   => $page,
            'editor' => $editor,
            'auth'   => auth(),
            'person' => auth()->user(),
        ]);

        if ($editor->isHistory()) {
            return view('boomcms::editor.toolbar.history', [
                'previous' => $page->getCurrentVersion()->getPrevious(),
                'next'     => $page->getCurrentVersion()->getNext(),
                'version'  => $page->getCurrentVersion(),
                'diff'     => new Diff(),
            ]);
        }

        $toolbarFilename = ($editor->isEnabled()) ? 'edit' : 'preview';

        return view("boomcms::editor.toolbar.$toolbarFilename");
    }
}
