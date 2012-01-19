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
			$page->template_id = $template;
			$page->save();
			
			// Add to the tree.
			$page->mptt->page_id = $page->id;
			
			// Where should we put it?
			$parent->add_child( $page );
			
			// Save the page.
			$page->save();
	
			// URI needs to be generated after the MPTT is set up.
			$uri = $page->generate_uri();
	
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
		$page = $this->_page;
		
		if (!$this->person->can( 'edit', $this->_page ))
			Request::factory( 'error/403' )->execute();
			
		$data = json_decode( Arr::get( $_POST, 'data' ));

		// If a version ID and publish is sent then all we're doing is making that version published.
		if (isset( $data->vid ) && isset( $data->publish ))
		{
			// TODO
			//if ($person->can( 'publish', $page ))
			//{
				$vid = (int) preg_replace( "/[^0-9]+/", "", $data->vid );
				$version = ORM::factory( 'version_page', $vid );
				
				// Check that the version belongs to the current page.
				if ($version->rid === $page->id)
				{
					$page->published_vid = $version->id;
					$page->save();
				}
			//}
			exit;
		}
		
		// Do editing page stuff.
		
		// Are we publishing this version?
		if (isset( $data->publish ))
		{
			// TODO
			//if ($person->can( 'publish', $page ))
			//{
				$page->published_vid = $page->active_vid;
				$page->save();
			//}
			exit;
		}
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
	
	public function action_revisions()
	{
		$v = View::factory( 'ui/subtpl_sites_revisions' );
		$v->page = $this->_page;
		
		echo $v;
		exit;
	}
}

?>
