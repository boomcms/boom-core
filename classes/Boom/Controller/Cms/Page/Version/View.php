<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Controller_Cms_Page_Version_View extends Controller_Cms_Page_Version
{
	public function action_embargo()
	{
		// Call the parent function to check permissions.
		parent::action_embargo();

		$this->template = View::factory("$this->viewDirectory/embargo", array(
			'version'	=>	$this->old_version,
		));
	}

	public function action_template()
	{
		parent::action_template();

		$manager = new \Boom\Template\Manager;
		$manager->createNew();
		$templates = $manager->getValidTemplates();

		$this->template = View::factory("$this->viewDirectory/template", array(
			'template_id'	=>	$this->old_version->template_id,
			'templates'	=>	 $templates
		));
	}
}