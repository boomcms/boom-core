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
		
		// Create a new page object.
		$page = ORM::factory( 'page' );
		$page->page_status = Model_Page::STATUS_INVISIBLE;	
		$page->title = 'Untitled';
		$page->version_status = Model_Page::STATUS_DRAFT;
		$page->template_id = ($parent->default_child_template_id)? $parent->default_child_template_id : $parent->template_id;
		$page->save();
		
		// Add the page to the tree.
		$mptt = ORM::factory( 'page_mptt' );
		$mptt->page_id = $page->id;
		$mptt->insert_as_last_child( $parent->mptt );
		$mptt->save();
		
		// URI needs to be generated after the MPTT is set up.
		$uri = $page->generateUri();
		
		$this->request->redirect( $uri );
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
	
	/**
	* Clone a page.
	*
	* @todo Clone the page's slots.
	*/
	public function action_clone()
	{		
		$oldpage = $this->_page;

		// Copy the versioned column values.
		$newpage = $oldpage->copy();
		
		// Save the new page.
		$newpage->save();
		
		// Add the new page to the tree.
		$mptt = ORM::factory( 'page_mptt' );
		$mptt->page_id = $newpage->id;
		$mptt->insert_as_next_sibling( $oldpage->mptt );
		$mptt->save();
		
		// Generate a unique URI for the new page.
		$uri = $newpage->generateUri();
		
		$this->request->redirect( $uri );
	}
	
	/**
	* Delete page controller.
	*/
	public function action_delete()
	{
		$this->_page->delete();		
		
		// Go back to the homepage.
		$this->request->redirect( '/' );
	}
}

?>
