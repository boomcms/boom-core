<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Editor\Editor as EditorObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Editor extends Controller
{
    public function setState(Request $request, EditorObject $editor)
    {
        $state = $request->input('state');
        $numericState = constant(EditorObject::class.'::'.strtoupper($state));

        $editor->setState($numericState);
    }

    /**
     * Displays the CMS interface with buttons for add page, settings, etc.
     * Called from an iframe when logged into the CMS.
     */
    public function getToolbar(EditorObject $editor, Page $page)
    {
        $toolbarFilename = ($editor->isEnabled()) ? 'toolbar' : 'toolbar_preview';

        View::share([
            'page'   => $page,
            'editor' => $this->editor,
            'auth'   => auth(),
            'person' => auth()->user(),
        ]);

        return view("boomcms::editor.$toolbarFilename");
    }
}
