<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller {
	
	public function before()
	{
		$this->template = View::factory( 'cms/standard_template' );
		
		$this->template->title = 'People Manager';
		$this->template->client = Kohana::$config->load( 'core' )->get( 'clientname' );
		
		// Work out which CSS files to include in the HTML.
		$css  = array();
		$base_uri = URL::base( $this->request );

		if (file_exists(APPPATH . "/static/site/css/main.css"))
			$css[] = $base_uri . 'css/main.css';
		else
			$css[] = $base_uri . 'sledge/css/main.css';
			
		if ($this->actual_person->version->emailaddress != 'guest@hoopassociates.co.uk')
			$css[] = $base_uri . 'sledge/css/cms.css';

		// Add the CSS array to the template.
		$this->template->css = $css;
		
		// Do the same with the JS.
		$js = array();
		
		$js[] = $base_uri . 'sledge/js/jquery.js';
		if (file_exists(APPPATH . "docroots/site/js/main_init.js"))
			$js[] = $base_uri . 'js/main_init.js';
		else
			$js[] = $base_uri . 'sledge/js/main_init.js';
			
		// Add the JS to the template.
		$this->template->js = $js;
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		
		View::bind_global( 'person', $this->person );
	}
	
	public function action_add()
	{
		
		
		
	}
	
	/**
	* People manager view person.
	*
	* @todo What if the person ID isn't valid?
	* @todo check permissions
	* @return void
	*/
	public function action_view()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		$activity = ORM::factory( 'activitylog' )->where( 'audit_person', '=', $person->id )->limit( '50' )->order_by( 'audit_time', 'desc' )->find();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/view' );
		$this->template->subtpl_main->person = $person;	
		$this->template->subtpl_main->activity = $activity;			
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{
		$people = ORM::factory( 'person' )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/index' );
		$this->template->subtpl_main->people = $people;	
	}
	
	public function after()
	{
		echo $this->template;
	}
	
}

?>