<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge CMS controller template.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Template_Site extends Controller_Template
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
		parent::before();
		
		// Add the header subtemplate.
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		
		// Add the main subtemplate.
		$this->template->subtpl_main = View::factory( $this->subtpl_main->filename );
		
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
		
		View::bind_global( 'page', $this->page );
	}
}

?>