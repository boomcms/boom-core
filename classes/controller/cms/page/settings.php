<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS Page settings controller
* Contains methods for editing page settings
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Page_Settings extends Controller_Cms_Page
{
	
	public function action_publishing()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_publishing' );
		$this->template->templates = ORM::factory( 'template' )->find_all();
	}
	
	public function action_seo()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_seo' );
	}
	
	public function action_tags()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_tags' );
	}
	
	public function action_feature()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_featureimage' );
	}
	
	public function action_information()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_information' );
	}
	
	public function action_security()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_security' );
	}
	
	public function action_children()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_childsettings' );
		$this->template->templates = ORM::factory( 'template' )->find_all();
	}
	
	public function action_admin()
	{
		$this->template = View::factory( 'ui/subtpl_sites_pagesettings_adminsettings' );
	}
	
	public function after()
	{
		$this->template->page = $this->_page;
		
		echo $this->template;
		exit;	
	}
}