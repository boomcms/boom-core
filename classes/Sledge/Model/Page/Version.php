<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Page_Version extends Model_Version
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array(
		'template'		=>	array('model' => 'Template', 'foreign_key' => 'template_id'),
		'person'		=>	array('model' => 'Person', 'foreign_key' => 'audit_person'),
		'image'		=>	array('model' => 'Asset', 'foreign_key' => 'feature_image'),
	);

	protected $_has_one = array(
		'page'		=> array('model' => 'Page', 'foreign_key' => 'id'),
	);

	protected $_has_many = array(
		'chunks'	=> array('through' => 'pages_chunks', 'foreign_key' => 'page_versions.id'),
	);

	protected $_table_columns = array(
		'id'							=>	'',
		'rid'							=>	'',
		'template_id'					=>	'',
		'default_child_template_id'		=>	'',
		'prompt_for_child_template'		=>	'',
		'title'							=>	'',
		'visible_from'					=>	'',
		'visible_to'					=>	'',
		'child_ordering_policy'			=>	'',
		'children_visible_in_leftnav'		=>	'',
		'children_visible_in_leftnav_cms'	=>	'',
		'visible_in_leftnav'				=>	'',
		'visible_in_leftnav_cms'			=>	'',
		'keywords'					=>	'',
		'description'					=>	'',
		'internal_name'					=>	'',
		'default_child_uri_prefix'			=>	'',
		'indexed'						=>	'',
		'hidden_from_search_results'		=>	'',
		'default_grandchild_template_id'	=>	'',
		'hidden_from_internal_links'		=>	'',
		'audit_person'					=>	'',
		'audit_time'					=>	'',
		'deleted'						=>	'',
		'feature_image'					=>	'',
		'published'						=>	'',
	);

	/**
	 * Hold the calculated thumbnail for this version.
	 * @see Model_Version_Sledge_Asset::thumbnail()
	 * @var Model_Asset
	 */
	protected $_thumbnail;

	/**
	* Filters for the versioned person columns
	* @link http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'title' => array(
				array('html_entity_decode'),
				array('urldecode'),
				array('trim'),
			),
			'keywords' => array(
				array('trim'),
			),
			'description' => array(
				array('trim'),
			),
	   );
	}

	/**
	* Get the page description.
	* Returns $this->description if set or the current page's standfirst if not.
	*
	* @return string The page description.
	* @todo Retrieval of 'standfirst' text chunk.
	*/
	public function get_description()
	{
		return $this->description;
	}

	public function get_keywords()
	{
		return $this->keywords;
	}

	/**
	* Does the page have a feature image set?
	*
	* @return bool
	*/
	public function has_image()
	{
		if ($this->feature_image == 0 OR ! $this->image->loaded())
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Returns a thumbnail for the current page.
	 * The thumbnail is the first image in the bodycopy.
	 *
	 * This function:
	 * * SHOULD always return an instance of Model_Asset
	 * * SHOULD return an instance of Model_Asset where the type property = Sledge_Asset::IMAGE (i.e. not return an asset representing a video or pdf)
	 * * SHOULD NOT return an asset which is unpublished when the current user is not logged in.
	 * * For guest users SHOULD return the first image in the body copy which is published.
	 * * Where there is no image (or no published image) in the bodycopy SHOULD return an empty Model_Asset
	 *
	 * @todo 	Need to write tests for the above.
	 * @return 	Model_Asset
	 */
	public function thumbnail()
	{
		// Try and get it from the $_thumbnail property to prevent running the code multiple times
		if ($this->_thumbnail !== NULL)
		{
			return $this->_thumbnail;
		}

		// Try and get it from the cache.
		// We don't have to worry about updating this cache - when the bodycopy is changed a new page version is created anyway.
		// So once the thumbnail for a particular page version is cached the cache should never have to be changed.
		$cache_key = 'thumbnail_for_page_versions:' . $this->id;

		if ( ! $asset_id = $this->_cache->get($cache_key))
		{
			// Get the standfirst for this page version.
			$chunk = Chunk::find('text', 'bodycopy', $this);

			if ( ! $chunk->loaded())
			{
				$asset_id = 0;
			}
			else
			{
				// Find the first image in this chunk.
				$query = DB::select('chunk_text_assets.asset_id')
					->from('chunk_text_assets')
					->join('assets', 'inner')
					->on('chunk_text_assets.asset_id', '=', 'assets.id')
					->join('asset_versions', 'inner')
					->on('assets.active_vid', '=', 'asset_versions.id')
					->order_by('position', 'asc')
					->limit(1)
					->where('chunk_text_assets.chunk_id', '=', $chunk->chunk_id)
					->where('asset_versions.type', '=', Sledge_Asset::IMAGE);

				// If the current user isn't logged in then make sure it's a published asset.
				if ( ! Auth::instance()->logged_in())
				{
					$query->where('asset_versions.visible_from', '<=', time())
						->where('status', '=', Model_Asset::STATUS_PUBLISHED);
				}

				// Run the query and get the result.
				$result = $query->execute()->as_array();
				$asset_id = (isset($result[0]))? $result[0]['asset_id'] : 0;
			}

			// Save it to cache.
			$this->_cache->set($cache_key, $asset_id);
		}

		// Return a Model_Asset object for this asset ID.
		return $this->_thumbnail = ORM::factory('Asset', $asset_id);
	}
}
