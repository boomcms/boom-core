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

		if ($parent->default_child_uri_prefix)
			$prefix = $parent->default_child_uri_prefix . '/';
		else
			$prefix = ($parent->loaded()) ? $parent->getPrimaryUri() . '/' : '';

		$append = 0;
		$start_uri = $prefix . URL::title( strtolower( $page->title ) );
		
		// If we're adding a page to the root node the URL ends up starting with a '/', which we don't want.
		// So check for and remove a '/' from the start of the URI.
		if ($start_uri[0] == '/')
			$start_uri = substr( $start_uri, 1 );
			
		// Get a unique URI.
		// This should be done as part of one of the models - for instance when we set the title property of the page.
		// For simplicity of getting adding a page working to some extent it's here for now, it can be moved later.
		do {
			$uri = ($append > 0)? $start_uri. $append : $start_uri;
			$append++;
			
			$exists = (int) DB::select( 'page_uri.id' )
						->from( 'page_uri' )
						->join( 'page_uri_v', 'inner' )
						->on('active_vid', '=', 'page_uri_v.id' )
						->where( 'uri', '=', $uri )
						->limit( 1 )
						->execute()
						->get( 'id' );
		} while ($exists !== 0);
		
		// Create a URI for the page.
		$page_uri = ORM::factory( 'page_uri' );
		$page_uri->uri = $uri;
		$page_uri->page_id = $page->id;
		$page_uri->primary_uri = true;
		$page_uri->save();
		
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
