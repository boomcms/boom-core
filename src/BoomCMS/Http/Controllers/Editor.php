<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Editor\Editor as EditorObject;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Page;
use Illuminate\Support\Facades\View;

class Editor extends Controller
{
    /**
     * Sets the page editor state.
     */
    public function postState()
    {
        $state = $this->request->input('state');
        $numericState = constant(EditorObject::class.'::'.strtoupper($state));

        if ($numericState === null) {
            throw new \Exception('Invalid editor state: :state', [
                ':state'    => $state,
            ]);
        }

        $this->editor->setState($numericState);
    }

    /**
     * Displays the CMS interface with buttons for add page, settings, etc.
     * Called from an iframe when logged into the CMS.
     * The ID of the page which is being viewed is given as a URL paramater (e.g. /boomscms/editor/toolbar/<page ID>).
     */
    public function getToolbar()
    {
        $page = Page::find($this->request->input('page_id'));
        $this->editor->setActivePage($page);

        $toolbarFilename = ($this->editor->isEnabled()) ? 'toolbar' : 'toolbar_preview';

        View::share([
            'page'   => $page,
            'editor' => $this->editor,
            'auth'   => auth(),
            'person' => auth()->user(),
        ]);

        return view("boomcms::editor.$toolbarFilename");
    }
}
