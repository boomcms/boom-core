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
		
		// If the page wasn't found by URI load the 404 page.
		if (!$page_uri->loaded() && $uri != 'error/404')
			$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', 'error/404' )->find();
		
		// Load the relevant page object.
		$page = ORM::factory( 'page', $page_uri->page_id );
		$page_type = ($this->mode == 'cms' && $this->person->can( 'edit', $page ))? 'cms' : 'site';
		
		// Hack.
		// If we're in write mode overwrite the standard template with the cms standard template.
		if ($page_type == 'cms' && !Arr::get( $_GET, 'state' ) == 'siteeditcms')
		{
			$this->template = View::factory( 'cms/standard_template' );
			$title = $page->title;
			$subtpl_topbar = View::factory( 'ui/subtpl_sites_topbar' );
			
			View::bind_global( 'title', $title );
			View::bind_global( 'subtpl_topbar', $subtpl_topbar );
		}
		
		// Decorate the page model with a page class.
		// This allows us to change what the page does depending on whether we're in cms or site mode
		// Without changing the page model itself.
		$this->page = Page::factory( $page_type, $page );
			
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
		$this->template->subtpl_main->subtpl_footer = View::factory( 'site/subtpl_footer' );
		$this->template->subtpl_main->subtpl_footer->footer_pages = $footer_page_objects;
		
		parent::after();
	}
}

?>
