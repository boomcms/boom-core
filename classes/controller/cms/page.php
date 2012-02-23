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
					$this->response->body( $this->_page->url() );
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
				
			$this->response->body( URL::base( $this->request ) . $uri );
		}
		else
		{
			$v = View::factory( 'cms/ui/site/page/add' );
			$v->templates = ORM::factory( 'template' )->find_all();
			$v->page = $this->_page;
			$this->response->body( $v );
		}
	}
	
	/**
	* Save the page.
	* This needs a jolly good refactoring.
	* This code looks like it could've come out of Sledge2, but it is actually new code.
	* Sorry :(
	*/
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
				
		// Update visibility seperately because it's not a versioned column.
		if (isset( $data->visible ) && $this->person->can( 'edit', $page, 'visible' ))
		{
			$page->visible = $data->visible;
		}
		
		// Set any versioned. properties.
		foreach( array_keys( $page->version->object() ) as $column )
		{
			if (isset( $data->$column ) && $this->person->can( 'edit', $page, $column ))
			{				
				if (
					$column == 'visible_in_leftnav' ||
					$column == 'visible_in_leftnav_cms' ||
					$column == 'children_visible_in_leftnav' ||
					$column == 'children_visible_in_leftnav_cms' ||
					$column == 'ssl_only'
				)
				{
					// Columns which inherit from the parent if an empty value is sent.
					$page->$column = ($data->$column == "")? $parent->$column : $data->$column;
				}
				else if ($column == 'child_ordering_policy' && isset( $data->child_ordering_direction ))
				{
					// A special case for the child ordering policy.
					$page->order_children( (int) $data->child_ordering_policy, $data->child_ordering_direction );
				}
				else if ($column == 'visible_from')
				{
					$page->$column = strtotime( $data->$column );
					$page->visible_to = (isset( $page->visible_to ))? strtotime( $data->visible_to ) : null;	
				}
				else
				{
					$page->$column = $data->$column;
				}
			}
		}
		
		// Change the page's primary URI.
		if (isset( $data->uri ) && $data->uri != $page->get_primary_uri() && $this->person->can( 'edit', $page, 'uri' ))
		{
			$page_uri = ORM::factory( 'page_uri' );
			$page_uri->uri = $data->uri;
			$page_uri->page_id = $page->id;
			$page_uri->primary_uri = true;
			$page_uri->save();
		}
				
		// Remember the old version ID.
		$old_version = $page->version;
		
		// If the version hasn't been changed, force it to create a new page version.
		// This is done so that we get a new version when editing slots.
		if (!$page->version->changed())
		{
			$page->version = $page->version->copy();
		}
		
		// Save the new settings.
		$page->save();	
		$new_vid = $page->version->id;
		
		// Update slots.
		
		foreach( $data->slots as $type => $obj )
		{
			foreach( get_object_vars( $obj ) as $name => $slot_data )
			{
				$chunk = ORM::factory( 'chunk' );
				$chunk->type = $type;		
				$chunk->slotname = $name;
		
				switch( $type )
				{
					case 'text':
						$chunk->data->text = $slot_data;
						break;
					case 'feature':
						$chunk->data->target_page_id = $slot_data;
						break;
					case 'asset':
						$chunk->data->asset_id = $slot_data;
						break;
					case 'linkset':
						break;
				}
		
				if ($chunk->save())
				{
					$page->version->add( 'chunks', $chunk );
				}
			}
		}

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
		
		$this->response->body( $page->url() );
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
		
			$this->response->body( URL::base( Request::current() ) );
		}
		else
		{
			$v = View::factory( 'cms/ui/site/page/delete' );
			$v->page = $this->_page;
			$this->response->body( $v );
		}
	}
	
	public function action_revisions()
	{
		$v = View::factory( 'cms/ui/site/page/revisions' );
		$v->page = $this->_page;
		
		$this->response->body( $v );
	}
}

?>
