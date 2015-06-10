<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controllers\Controller;

use Illuminate\Support\Facades\View;

class Editor extends Controller
{
    /**
	 * Sets the page editor state.
	 */
    public function state()
    {
        $state = $this->request->input('state');
        $numericState = constant("\BoomCMS\Core\Editor\Editor::" . strtoupper($state));

        if ($numericState === null) {
            throw new \Exception("Invalid editor state: :state", [
                ':state'    =>    $state,
            ]);
        }

        $this->editor->setState($numericState);
    }

    /**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
    public function toolbar(Page\Provider $provider)
    {
        $page = $provider->findById($this->request->input('page_id'));
        $this->editor->setActivePage($page);

        if ($this->editor->isEnabled()) {
            $toolbarFilename = 'toolbar';
            $this->_add_readability_score_to_template($page);
        } else {
            $toolbarFilename = 'toolbar_preview';
        }

        View::share('page', $page);
        View::share('editor', $this->editor);

        return View::make("boom::editor.$toolbarFilename");
    }

    protected function _add_readability_score_to_template(Page\Page $page)
    {
        $readability = new Page\ReadabilityScore($page);
        View::share('readability', $readability->getSmogScore());
    }
}
