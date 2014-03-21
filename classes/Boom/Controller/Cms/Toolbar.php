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
		$mode = $this->request->query('mode');
		$mode OR $mode = 'block';

		$this->_toolbar = new View("$this->_view_directory/text", array('mode' => $mode));
	}

	public function after()
	{
		$this->response->body($this->_toolbar);

		parent::after();
	}
}