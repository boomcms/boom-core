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
	}
	
	public function action_add()
	{
		if (isset( $_POST['parent_id'] ) && isset( $_POST['template_id'] ))
		{
			// Find the parent page.
			$parent = ORM::factory( 'page', Arr::get( $_POST, 'parent_id', 1 ));
			
			// Check for add permissions on the parent page.
			if (!$this->person->can( 'add', $parent ))
				Request::factory( 'error/403' )->execute();
				
			// Which template to use?
			$template = Arr::get( $_POST, 'template_id' );
			if (!$template)
			{
				// Inherit from parent.
				$template = ($parent->default_child_template_id != 0)? $parent->default_child_template_id : $parent->template_id;
			}
	
			// Create a new page object.
			$page = ORM::factory( 'page' );
			$page->page_status = Model_Page::STATUS_INVISIBLE;	
			$page->title = 'Untitled';
			$page->version_status = Model_Page::STATUS_DRAFT;
			$page->template_id = $template;
			$page->save();
			
			// Add to the tree.
			$page->mptt->page_id = $page->id;
			$page->mptt->insert_as_last_child( $parent->mptt );
			
			// Save the page.
			$page->save();
	
			// URI needs to be generated after the MPTT is set up.
			$uri = $page->generateUri();
	
			echo URL::base( $this->request ) . $uri;
		}
		else
		{
			$v = View::factory( 'ui/subtpl_sites_page_add' );
			$v->templates = ORM::factory( 'template' )->find_all();
			$v->page = $this->_page;
			echo $v;
		}
		
		exit;
	}
	
	
	public function action_save()
	{
		if (!$this->person->can( 'edit', $this->_page ))
			Request::factory( 'error/403' )->execute();
			
		$page = $this->_page;
		
		$page->template_rid = $template_rid;
		$page->default_child_template_rid = $default_child_template_rid;
		$page->prompt_for_child_template = $prompt_for_child_template;
		$page->setParent( $parent );
		$page->setTitle( $title );	
		$page->visiblefrom_timestamp = $visibilefrom_timestamp;
		$page->visiblveto_timestamp = $visibleto_timestamp;
	}
	
	/**
	* Clone a page.
	*
	* @todo Clone the page's slots.
	*/
	public function action_clone()
	{	
		if (!$this->person->can( 'clone', $this->_page ))
			Request::factory( 'error/403' )->execute();
				
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
		if (!$this->person->can( 'delete', $this->_page ))
			Request::factory( 'error/403' )->execute();		
			
		if ( Request::current()->method() == 'POST' )
		{
			$this->_page->delete();
		
			echo URL::base( Request::current() );
		}
		else
		{
			$v = View::factory( 'ui/subtpl_sites_page_delete' );
			$v->page = $this->_page;
			echo $v;
		}
		
		exit;
	}
	
	/**
	* Undo the last page edit
	*/
	public function action_undo()
	{
		if (!$this->person->can( 'edit', $this->_page ))
			Request::factory( 'error/403' )->execute();		
			
		$version = DB::select( 'page_v.id' )
						->where( 'rid', '=', $this->_page->id )
						->order_by( 'id', 'desc' )
						->limit( 1 )
						->offset( 1 )
						->execute();
		
		$version_id = $version->get( 'id' );
		$this->_page->active_vid = $version_id;
		$this->_page->save();
	}
	
	public function action_revisions()
	{
		$page = $this->_page;
		$versions = ORM::factory( 'version_page' )->where( 'rid', '=', $page->id )->find_all();
		
		$v = View::factory( 'ui/subtpl_sites_revisions' );
		$v->page = $page;
		$v->versions = $versions;
		
		echo $v;
		exit;
	}
}

?>
