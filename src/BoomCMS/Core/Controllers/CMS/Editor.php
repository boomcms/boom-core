<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Page;

class Editor extends Boom\Controller
{
    /**
	 * Sets the page editor state.
	 */
    public function state()
    {
        $state = $this->request->input('state');
        $numeric_state = constant("\BoomCMS\Core\Editor\Editor::" . strtoupper($state));

        if ($numeric_state === null) {
            throw new Kohana_Exception("Invalid editor state: :state", [
                ':state'    =>    $state,
            ]);
        }

        $this->editor->setState($numeric_state);
    }

    /**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
    public function toolbar(Page\Provider $provider)
    {
        $page = $provider->findById($this->request->param('id'));

        $editable = $this->editor->isEnabled();

        $this->auth->cache_permissions($page);

        $toolbar_filename = ($editable) ? 'toolbar' : 'toolbar_preview';
        return View::make("boom/editor/$toolbar_filename");

        $editable && $this->_add_readability_score_to_template($page);

        View::bind_global('page', $page);
    }

    protected function _add_readability_score_to_template(Page $page)
    {
        $readability = new \Boom\Page\ReadabilityScore($page);
        $this->template->set('readability', $readability->getSmogScore());
    }
}
