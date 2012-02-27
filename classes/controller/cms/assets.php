<?php defined('SYSPATH') or die('No direct script access.');

/**
* Asset controller
* Contains methods for adding / replacing an asset etc.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_Assets extends Controller_Cms
{	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage assets' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'Asset Manager';
		$subtpl_topbar = View::factory( 'cms/ui/assets/topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
	}
	
	/**
	* Show assets by tag
	*
	* @todo Use the tag MPTT tree to find assets which are tagged with child tags of the supplied tag.
	*/
	public function by_tag()
	{
		$tag_id = $this->request->param( 'id' );
		$tag = ORM::factory( 'tag', $tag_id );
		
		// Check that the tag exists.
		if ($tag->loaded())
		{
			$assets = ORM::factory( 'asset' )
						->join( 'tagged_object', 'inner' )
						->on( 'tag_id', '=', $tag->id )
						->where( 'object_type', '=', Model_Tagged_Object::OBJECT_TYPE_ASSET )
						->limit( 10 )
						->find_all();
						
			// Output the data.
			// Need to find out which format this needs to be done in.		
		}
	}
	
	/**
	* Delete controller
	* Allows deleting multiple assets
	*
	*/
	public function action_delete()
	{
		$asset_ids = explode( ",", Arr::get( $_REQUEST, 'assets' ) );
		
		foreach( $asset_ids as $asset_id )
		{
			$asset = ORM::factory( 'asset', $asset_id );
			$asset->delete();
		}
		
		$this->request->redirect( '/cms/assets' );		
	}
	
	
	/**
	* Download controller.
	* Allows downloading of assets in archived format.
	* Method can be zip, tgz, tbz2
	*/
	public function action_download()
	{
		$asset_ids = explode( ",", Arr::get( $_GET, 'assets' ) );
		$assets = count( $asset_ids );
		
		if ($assets > 1 )
		{
			// Multi-asset download.
			$method = Arr::get( $_GET, 'method' );
		
			// Do the download.
			if ($method == 'zip')
			{
				$archive = new ZipArchive();
			
				foreach ($asset_ids as $asset_id )
				{
					$asset = ORM::factory( 'asset', $asset_id );
					if ($asset->loaded())
					{
						$archive->addFile( ASSETPATH . $asset->id, $asset->filename );
					}
				}
			
				$archive->close();
			}
		
			// TODO: Zip download.	
		}
		else if ($assets === 1)
		{
			// Download a single asset.
			$asset = ORM::factory( 'asset', $asset_ids[0] );
			if ($asset->loaded())
			{
		        $this->response->headers( 'Expires', gmdate("D, d M Y H:i:s",time()+(3600*7))." GMT" );
		        $this->response->headers( 'Content-Type', $asset->get_mime() );
		        $this->response->headers( 'Content-Transfer-Encoding', 'binary' );
		        $this->response->headers( 'Content-Length', $asset->get_filesize() );
		        $this->response->headers( 'Content-Disposition',  'attachment; filename="'.basename($asset->filename) );

		        $this->response->body( readfile( ASSETPATH . $asset->id ) );
			}	
		}	
	}
	
	/**
	* Asset replace controller
	*
	* @uses Asset::is_supported()
	*/
	public function action_replace()
	{
		$asset_id = preg_replace( '/[^0-9]+/', '', $this->request->param( 'id' ) );
		$asset = ORM::factory( 'asset', $asset_id );
				
		if ($asset->loaded())
		{
			if ($this->request->method() == 'POST')
			{
				if (isset( $_FILES['file'] ))
				{
					$file = $_FILES['file'];
					
					if (in_array( $file['type'], Asset::$allowed_types ))
					{
						$asset->filename = $file['name'];
					
						// TODO: this needs to work for any asset type.
						$asset->type = 'image';
						$asset->save();
			
						Upload::save( $file, $asset->id, ASSETPATH );
					}
				}
			
				$this->request->redirect( '/cms/assets' );
			}
			else
			{
				$v = View::factory( 'cms/ui/assets/replace_asset' );
				$v->asset = $asset;
				$this->response->body( $v );
			}
		}
	}
	
	/**
	* Asset upload controller
	*
	* @uses Asset::is_supported()
	*/
	public function action_upload()
	{
		if ($this->request->method() == 'POST')
		{
			foreach( $_FILES as $file)
			{
				if (in_array( $file['type'], Asset::$allowed_types ))
				{
					$asset = ORM::factory( 'asset' );
					$asset->filename = $file['name'];
					$asset->title = 'Untitled Asset';
					
					// TODO: this needs to work for any asset type.
					$asset->type = 'image';
					$asset->save();
			
					Upload::save( $file, $asset->id, ASSETPATH );
				}
			}
			
			$this->request->redirect( '/cms/assets' );
		}
		else
		{
			$this->response->body( View::factory( 'cms/ui/assets/upload_assets' )->render() );
		}
	}

	public function action_edit()
	{
		
		
	}
	
	public function action_index()
	{		
		$page = Arr::get( $_REQUEST, 'page', 1 );
		$tag = Arr::get( $_REQUEST, 'tag' );
		if (!$tag)
		{
			$tag = ORM::factory( 'tag' )->find_by_route( 'tags/assets' );
		}
		
		$query = ORM::factory( 'asset' )->where( 'tag', '=', $tag )->where( 'deleted', '=', false );
		
		$pages = ceil( $query->count_all() / 10 );
		$assets = $query->limit( 10 )->where( 'tag', '=', $tag )->offset( ($page - 1) * 10 )->find_all();
				
		$pagination = View::factory( 'pagination' );
		$pagination->total_pages = $pages;
		$pagination->current_page = $page;
		$pagination->previous_page = $page - 1;
		$pagination->next_page = $page + 1;
		$pagination->base_url = '';
		
		$this->template->subtpl_main = View::factory( 'cms/ui/assets/manager' );
		$this->template->subtpl_main->assets = $assets;
		$this->template->subtpl_main->state = Arr::get( Request::current()->post(), 'state', 'collapsed' );
		$this->template->subtpl_main->pagination = $pagination;
	}
	
	/**
	* Controller to show an assets detailed view.
	*
	*/
	public function action_view()
	{
		$asset_id = $this->request->param( 'id' );
		$asset = ORM::factory( 'asset', $asset_id );
		
		if ($asset->loaded())
		{
			$this->template->subtpl_main = View::factory( 'cms/ui/assets/detailview' );
			$this->template->subtpl_main->asset = $asset;
		}
		else
		{
			Request::factory( 'error/404' )->execute();
		}
	}
}

?>
