<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Editor\Editor;
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
        $numericState = constant(Editor::class.'::'.strtoupper($state));

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
     * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>).
     */
    public function getToolbar()
    {
        $page = Page::findById($this->request->input('page_id'));
        $this->editor->setActivePage($page);

        View::share('page', $page);
        View::share('editor', $this->editor);

        $toolbarFilename = ($this->editor->isEnabled()) ? 'toolbar' : 'toolbar_preview';

        return View::make("boomcms::editor.$toolbarFilename");
    }
}
