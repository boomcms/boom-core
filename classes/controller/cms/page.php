<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS Page controller
* Contains methods for adding / saving a page etc.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Page extends Controller_Cms
{
	/**
	* Object representing the current page.
	* 
	* @var object
	* @access private
	*/
	private $_page;
	
	/**
	* Load the current page.
	* All of these methods should be called with a page ID in the params
	* Before the methods are called we find the page so it can be used, clever eh?
	*
	* @return void
	*/	
	public function before()
	{
		parent::before();
		
		$page_id = $this->request->param( 'id' );
		$page_id = (int) preg_replace( "/[^0-9]+/", "", $page_id );
		$this->_page = ORM::factory( 'page', $page_id );
	
		if (!$this->_page->loaded())
		{
			// Do something.
			exit();
		}
	}
	
	public function action_add()
	{
		$parent = $this->_page;
		
		// Find the URI we were called with.
		$uri = $this->request->initial()->uri();
		
		// Create a new page object.
		$page = ORM::factory( 'page' );
		
		//$page_uri = ORM::factory( 'page_uri' );
		//$page_uri->version->uri = $uri;
		
		//$page->add( $page_uri );
		$page->title = 'Untitled';
		
		echo "Adding a page at " . URL::base( $this->request ) . $uri . "!";
	}
	
	
	public function action_save()
	{
		$page = $this->_page;
		
		$page->version->template_rid = $template_rid;
		$page->version->default_child_template_rid = $default_child_template_rid;
		$page->version->prompt_for_child_template = $prompt_for_child_template;
		$page->version->setParent( $parent );
		$page->version->setTitle( $title );	
		$page->version->visiblefrom_timestamp = $visibilefrom_timestamp;
		$page->version->visiblveto_timestamp = $visibleto_timestamp;
	}
	
	public function action_clone()
	{		
		$oldpage = $this->_page;

		// Copy the versioned column values.
		$newpage = ORM::factory( 'page' );

		foreach( array_keys( $page->version->object() ) as $column )
		{
			if ($column != $page->version->primary_key())
			{
				$newpage->version->$column = $page->$column;			
			}
		}
	}
	
	/**
	* Delete page controller.
	*/
	public function action_delete()
	{
		// Call Model_Page::delete().
		// Sets the version deleted column to true for this page and it's children.
		// Also deletes the pages from the MPTT tree.
		// Automatically calls Model_Page::save() to save changes.
		$this->_page->delete();
		
		// Go back to the homepage.
		Request::factory( '/' )->execute();
	}
}

?>
