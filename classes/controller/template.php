<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge controller template.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Template extends Kohana_Controller_Template
{
	public $auto_render = false;
	
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'site/standard_template';
	
	public function before()
	{
        // Load the template
        $this->template = View::factory( $this->template );

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
		
		// Add the header subtemplate.
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		
		// Add the main subtemplate.
		$this->template->subtpl_main = View::factory( $this->subtpl_main->version->filename );
		
		// Footer templates
		$subtpl_footer = View::factory( 'site/subtpl_footer' );
		$footer_page_objects = array();

		$footer_pages = array('contact', 'newsletter', 'rssfeeds');
		foreach ($footer_pages as $internal_name)
		{
			$p = ORM::factory( 'page' )->with( 'version' )->where( 'internal_name', '=', $internal_name)->find();
			
			if ($p->loaded())
			{
				$footer_page_objects[] = $p;
			}
		}
		$this->template->subtpl_main->subtpl_footer = $subtpl_footer;
		$this->template->subtpl_main->subtpl_footer->footer_pages = $footer_page_objects;
		
		// Set some variables.
		View::bind_global( 'page', $this->page );
		
	    parent::before();
	}
}

?>