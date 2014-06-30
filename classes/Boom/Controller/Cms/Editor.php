<?php

class Boom_Controller_Cms_Editor extends Boom_Controller
{
	/**
	 * Sets the page editor state.
	 */
	public function action_state()
	{
		$state = $this->request->post('state');
		$numeric_state = constant("\Boom\Editor::" . strtoupper($state));

		if ($numeric_state === null)
		{
			throw new Kohana_Exception("Invalid editor state: :state", array(
				':state'	=>	$state,
			));
		}

		$this->editor->setState($numeric_state);
	}

	/**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
	public function action_toolbar()
	{
		$page =  \Boom\Page\Factory::byId($this->request->param('id'));
		$editable = $this->editor->isEnabled();

		$this->auth->cache_permissions($page);

		$toolbar_filename = ($editable)? 'toolbar' : 'toolbar_preview';
		$this->template = View::factory("boom/editor/$toolbar_filename");

		$editable && $this->_add_readability_score_to_template($page);

		View::bind_global('page', $page);
	}

	protected function _add_readability_score_to_template(\Boom\Page $page)
	{
		$readability = new \Boom\Page\ReadabilityScore($page);
		$this->template->set('readability', $readability->getSmogScore());
	}
}