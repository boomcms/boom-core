<?php

class Boom_Controller_Cms_Toolbar extends Boom_Controller
{
	/**
	 *
	 * @var View
	 */
	protected $_toolbar;

	protected $_view_directory = 'boom/toolbars';

	public function action_asset()
	{
		$this->_toolbar = new View("$this->_view_directory/asset");
	}

	public function action_text()
	{
		$this->_toolbar = new \Boom\TextEditorToolbar($this->request->query('mode'));
		$this->_toolbar->render();
	}

	public function after()
	{
		$this->response->body($this->_toolbar);

		parent::after();
	}
}