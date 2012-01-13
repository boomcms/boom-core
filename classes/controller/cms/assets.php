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
		$asset_ids = explode( ",", Arr::get( $_POST, 'assets' ) );
		
		foreach( $asset_ids as $asset_id )
		{
			$asset = ORM::factory( 'asset', $asset_id );
			$asset->delete();
		}
		
		exit;		
	}
	
	
	/**
	* Download controller.
	* Allows downloading of assets in archived format.
	* Method can be zip, tgz, tbz2
	*/
	public function action_download()
	{
		$asset_ids = explode( ",", Arr::get( $_GET, 'assets' ) );
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
	
	/**
	* Asset upload controller
	*
	* @uses Asset::is_supported()
	*/
	public function action_upload()
	{
		
		
	}
	
	
	public function action_replace()
	{

	}

	public function action_edit()
	{
		
		
	}
	
	public function action_index()
	{
		$this->template->subtpl_main = View::factory( 'cms/pages/assets/index' );
		$this->template->subtpl_topbar = View::factory( 'ui/subtpl_assets_topbar' );
		
		$assets = ORM::factory( 'asset' )->find_all()->as_array();
		$this->template->subtpl_main->assets = $assets;
	}
	
	public function after()
	{	
		$this->template->title = 'Asset Manager';
		
		parent::after();
	}
}

?>
