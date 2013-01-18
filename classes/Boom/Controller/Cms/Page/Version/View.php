<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Controller_Cms_Page_Version_View extends Controller_Cms_Page_Version
{
	public function action_embargo()
	{
		// Call the parent function to check permissions.
		parent::action_embargo();

		$this->template = View::factory("$this->_view_directory/embargo", array(
			'version'	=>	$this->old_version,
		));
	}

	public function action_feature()
	{
		// Call the parent function to check permissions.
		parent::action_feature();

		$this->template = View::factory("$this->_view_directory/feature", array(
			'feature_image_id'	=>	$this->old_version->feature_image_id,
		));
	}

	public function action_template()
	{
		// Call the parent function to check permissions.
		parent::action_template();

		$this->template = View::factory("$this->_view_directory/template", array(
			'template_id'	=>	$this->old_version->template_id,
			'templates'	=>	 ORM::factory('Template')->names()
		));
	}
}