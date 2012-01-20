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
	protected $_page;
	
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
		
		// Most of these methods can be  sent a version ID.
		// This allows viewing an old version and then editing / publishing that version.
		// Most of the time the vid will be page's current version ID - i.e. the user is viewing page as standard.
		// So check the vid, and load an older version if necessary.
		$data = json_decode( Arr::get( $_POST, 'data' ));
		if (isset( $data->vid ))
		{
			$vid = $data->vid;
		}
		else if ( Arr::get( $_GET, 'vid' ))
		{
			$vid = Arr::get( $_GET, 'vid' );
		}
		
		if (isset( $vid ))
		{
			if ($this->_page->active_vid != $vid)
			{
				$version = ORM::factory( 'version_page', $vid );
			
				// Check that the version belongs to the current page.
				if ($version->rid === $this->_page->id)
				{
					$this->_page->version = $version;
				}
				else
				{
					echo $this->_page->url();
					exit;
				}
			}
		}
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
			$page->visible = false;	
			$page->title = 'Untitled';
			$page->visible_in_leftnav = $parent->children_visible_in_leftnav;
			$page->visible_in_leftnav_cms = $parent->children_visible_in_leftnav_cms;
			$page->children_visible_in_leftnav = $parent->children_visible_in_leftnav;
			$page->children_visible_in_leftnav_cms = $parent->children_visible_in_leftnav_cms;
			$page->ssl_only = $parent->ssl_only;
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
		if (!$page->mptt->is_root())
		{
			$parent = $page->mptt->parent()->page;
		}
		
		if (!$this->person->can( 'edit', $page ))
			Request::factory( 'error/403' )->execute();
			
		$data = json_decode( Arr::get( $_POST, 'data' ));
				
		// Do editing page stuff.
		//if (isset( $data->title ))
		//	$page->title = $data->title;
			
		// SEO tab.
		if (isset( $data->description ))
			$page->description = $data->description;
			
		if (isset( $data->hidden_from_search_results ))
			$page->hidden_from_search_results = (bool) $data->hidden_from_search_results;
			
		if (isset( $data->indexed ))
			$page->indexed = (bool) $data->indexed;
			
		if (isset( $data->keywords ))
			$page->keywords = $data->keywords;
			
		// Publishing tab.
		//if (isset( $data->parent_id ))
		// TODO: reparent page.
		
		if (isset( $data->visible ))
			$page->visible = (bool) $data->visible;
		
		if (isset( $data->template ))
			$page->template_id = (int) preg_replace( "/[^0-9]+/", "", $data->template );
			
		if (isset( $data->enable_rss ))
			$page->enable_rss = (bool) $data->enable_rss;
			
		//if (isset( $data->uri ))
		// TODO change page uri.
		
		if (isset( $data->visible_from ))
			$page->visible_from = strtotime( $data->visible_from );
			
		if (isset( $data->visible_to ))
			$page->visible_to = ($data->visible_to == "")? null : strtotime( $data->visible_to );
			
		if (isset( $data->visible_in_leftnav ))
			$page->visible_in_leftnav = ($data->visible_in_leftnav == "")? $parent->children_visible_in_leftnav : (bool) $data->visible_in_leftnav;
			
		if (isset( $data->visible_in_leftnav_cms ))
			$page->visible_in_leftnav_cms = ($data->visible_in_leftnav_cms == "")? $parent->children_visible_in_leftnav_cms : (bool) $data->visible_in_leftnav_cms;
			
		// Child page settings tab.
		if (isset( $data->children_visible_in_leftnav ))
			$page->children_visible_in_leftnav = ($data->children_visible_in_leftnav = "")? $parent->children_visible_in_leftnav : (bool) $data->children_visible_in_leftnav;
			
		if (isset( $data->children_visible_in_leftnav_cms ))
			$page->children_visible_in_leftnav_cms = ($data->children_visible_in_leftnav_cms = "")? $parent->children_visible_in_leftnav_cms : (bool) $data->children_visible_in_leftnav_cms;
			
		//if (isset( $data->default_child_default_child_template_id ))
		//	$page->default_child_default_child_template_id = $data->default_child_default_child_template_id;
			
		if (isset( $data->default_child_template_id ))
			$page->default_child_template_id = $data->default_child_template_id;
			
		if (isset( $data->default_child_uri_prefix ))
			$page->default_child_uri_prefix = $data->default_child_uri_prefix;
			
		//if (isset( $data->pagetype_parent_id ))
		//	$page->pagetype_parent_id = $data->pagetype_parent_id;
			
		if (isset( $data->child_ordering_policy ) && isset( $data->child_ordering_direction ))
			$page->order_children( (int) $data->child_ordering_policy, $data->child_ordering_direction );
			
		// Admin settings tab.
		if (isset( $data->internal_name ))
			$page->internal_name = $data->internal_name;
			
		if (isset( $data->pagetype_description ))
			$page->pagetype_description = $data->pagetype_description;
			
		if (isset( $data->ssl_only ))
			$page->ssl_only = ($data->ssl_only = "")? $parent->ssl_only : (bool) $data->ssl_only;
		
		// Remember the old version ID.
		$old_vid = $page->version->id;
		
		// Save the new settings.
		$page->save();	
		
		// Copy any slots to the new version.
		$query = DB::query( Database::INSERT, "insert into chunk_page (chunk_id, page_vid) select chunk_id, :new_vid from chunk_page where page_vid = :old_vid" );
		$new_vid = $page->version->id;
		$query->bind( ':new_vid', $new_vid );
		$query->bind( ':old_vid', $old_vid );
		$query->execute();
		
		// Are we publishing this version?
		if (isset( $data->publish ))
		{
			// TODO
			//if ($person->can( 'publish', $page ))
			//{
				$page->published_vid = $page->version->id;
				$page->save();
			//}
		}
		
		echo $page->url();
		exit;
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
