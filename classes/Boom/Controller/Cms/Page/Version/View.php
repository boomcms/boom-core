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
		parent::action_feature();

		$images_in_page = new \Boom\Page\AssetsUsed($this->old_version);
		$images_in_page->setType(Boom_Asset::IMAGE);

		$this->template = View::factory("$this->_view_directory/feature", array(
			'feature_image_id' => $this->old_version->feature_image_id,
			'images_in_page' => $images_in_page->getAll(),
		));
	}

	public function action_template()
	{
		parent::action_template();

		$manager = new Template_Manager;
		$manager->create_new();
		$templates = $manager->get_valid_templates();

		$this->template = View::factory("$this->_view_directory/template", array(
			'template_id'	=>	$this->old_version->template_id,
			'templates'	=>	 $templates
		));
	}
}