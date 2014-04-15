<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Editor extends Boom_Controller
{
	/**
	 * Sets the page editor state.
	 */
	public function action_state()
	{
		$state = $this->request->post('state');
		$numeric_state = constant("Editor::" . strtoupper($state));

		if ($numeric_state === null)
		{
			throw new Kohana_Exception("Invalid editor state: :state", array(
				':state'	=>	$state,
			));
		}

		$this->editor->state($numeric_state);
	}

	/**
	 * Displays the CMS interface with buttons for add page, settings, etc.
	 * Called from an iframe when logged into the CMS.
	 * The ID of the page which is being viewed is given as a URL paramater (e.g. /cms/editor/toolbar/<page ID>)
	 */
	public function action_toolbar()
	{
		$page = new Model_Page($this->request->param('id'));
		$editable = $this->editor->state_is(Editor::EDIT);

		$this->auth->cache_permissions($page);

		$toolbar_filename = ($editable)? 'toolbar' : 'toolbar_preview';
		$this->template = View::factory("boom/editor/$toolbar_filename");

		$editable AND $this->_add_readability_score_to_template($page);

		View::bind_global('page', $page);
	}

	protected function _add_readability_score_to_template(Model_Page $page)
	{
		$readability = new Page_ReadabilityScore($page);
		$this->template->set('readability', $readability->get_smog_score());
	}
}