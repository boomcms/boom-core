<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS Page controller
* Contains methods for adding / saving a page etc.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Page extends Controller_Template
{

	public function action_add()
	{
		// Find the URI we were called with.
		$uri = $this->request->uri();
		
		// Create a new page object.
		$page = ORM::factory( $page );
		
		$page_uri = ORM::factory( 'page_uri' );
		$page_uri->version->uri = $uri;
		
		$page->add( $page_uri );
		$page->version->title = 'Untitled';
		
		echo $this->template;
	}
	
	
	public function action_save()
	{
		$page = ORM::factory( 'page', $page_id );
		$page->version->template_rid = $template_rid;
		$page->version->default_child_template_rid = $default_child_template_rid;
		$page->version->prompt_for_child_template = $prompt_for_child_template;
		$page->version->setParent( $parent );
		$page->version->setTitle( $title );	
		$page->version->visiblefrom_timestamp = $visibilefrom_timestamp;
		$page->version->visiblveto_timestamp = $visibleto_timestamp;
	}
	
	public function action_index()
	{
		die( 'cms page index' );
	}
}

?>