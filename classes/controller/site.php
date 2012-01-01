<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge CMS controller template.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Site extends Sledge_Controller
{
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'site/standard_template';
	
	/**
	* The requested page.
	* @access protected
	* @var object
	*/
	protected $page;
	
	public function before()
	{
		parent::before();
		
		// All pages and templates are hard coded for the CMS so this is all site specific.
		// Find the requested page.
		$uri = ($this->request->initial()->uri() == '/')? '' : $this->request->initial()->uri();
		$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
		
		// Load the relevant page object.
		$this->page = ORM::factory( 'page', $page_uri->page_id );	
		
		// If the page wasn't found by URI load the 404 page.
		// TODO: check that the requested URI wasn't the 404 page or we end up in an infinate loop.
		if (!$this->page->loaded())
			Request::factory( 'error/404' )->execute();
			
		// Load the relevant template object.
		$template = ORM::factory( 'template', $this->page->template_id );

		// Add the main subtemplate.
		$this->template->subtpl_main = View::factory( $template->filename );
		View::bind_global( 'page', $this->page );
	}
	
	public function after()
	{	
		// Add the header subtemplate.
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		
		// Footer templates
		$subtpl_footer = View::factory( 'site/subtpl_footer' );
		$footer_page_objects = array();

		$footer_pages = array('contact', 'newsletter', 'rssfeeds');
		foreach ($footer_pages as $internal_name)
		{
			$p = ORM::factory( 'page' )->where( 'internal_name', '=', $internal_name)->find();
			
			if ($p->loaded())
			{
				$footer_page_objects[] = $p;
			}
		}
		$this->template->subtpl_main->subtpl_footer = $subtpl_footer;
		$this->template->subtpl_main->subtpl_footer->footer_pages = $footer_page_objects;
		
		parent::after();
	}
}

?>
