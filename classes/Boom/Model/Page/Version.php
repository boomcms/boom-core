<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Page_Version extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array(
		'template'		=>	array('model' => 'Template', 'foreign_key' => 'template_id'),
		'person'		=>	array('model' => 'Person', 'foreign_key' => 'edited_by'),
	);

	protected $_has_many = array(
		'chunks'	=> array('through' => 'page_chunks', 'foreign_key' => 'page_vid'),
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'page_id'			=>	'',
		'template_id'		=>	'',
		'title'				=>	'',
		'edited_by'		=>	'',
		'edited_time'		=>	'',
		'page_deleted'		=>	'',
		'feature_image_id'	=>	'',
		'published'			=>	'',
		'embargoed_until'	=>	'',
		'stashed'			=>	'',
	);

	protected $_table_name = 'page_versions';

	/**
	 * Hold the calculated thumbnails for this version.
	 *
	 * @see Model_Page_Version::thumbnail()
	 * @var array
	 */
	protected $_thumbnails = array();

	protected $_updated_column = array(
		'column'	=>	'edited_time',
		'format'	=>	TRUE,
	);

	/**
	 * Copies the chunks from another page version to this version.
	 *
	 * @param Model_Page_Version $from_version
	 * @param array $exclude An array of slotnames which shouldn't be copied from the other version.
	 * @return Model_Page_Version
	 */
	public function copy_chunks(Model_Page_Version $from_version, array $exclude = NULL)
	{
		foreach (array('asset', 'text', 'feature', 'linkset', 'slideshow') as $type)
		{
			$subquery = DB::select(DB::expr($this->id), 'chunk_id')
				->from('page_chunks')
				->join("chunk_$type"."s")
				->on('page_chunks.chunk_id', '=', 'id')
				->where('page_chunks.page_vid', '=', $from_version->id);

			if ( ! empty($exclude))
			{
				$subquery->where('slotname', 'not in', $exclude);
			}

			DB::insert('page_chunks', array('page_vid', 'chunk_id'))
				->select($subquery)
				->execute($this->_db);
		}

		return $this;
	}

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
	 * Validation rules
	 *
	 * @return	array
	 */
	public function rules()
	{
		return array(
			'page_id'	=>	array(
				array('not_empty'),
				array('numeric'),
			),
			'template_id'	=>	array(
				array('not_empty'),
				array('numeric'),
			),
			'title'	=>	array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Returns a thumbnail for the current page version.
	 * The thumbnail is the first image in the specified chunk.
	 *
	 * @param	$slotname		The slotname of the chunk to look for an image in. Default is to look in the bodycopy.
	 * @return 	Model_Asset
	 * @uses		Model_Page_Version::$_thumbnails
	 */
	public function thumbnail($slotname = 'bodycopy')
	{
		// Try and get it from the $_thumbnail property to prevent running the code multiple times
		if (isset($this->_thumbnails[$slotname]))
		{
			return $this->_thumbnails[$slotname];
		}

		// Get the standfirst for this page version.
		$chunk = Chunk::find('text', $slotname, $this);

		if ( ! $chunk->loaded())
		{
			return $this->_thumbnails[$slotname] = new Model_Asset;
		}
		else
		{
			// Find the first image in this chunk.
			$query = ORM::factory('Asset')
				->join('chunk_text_assets')
				->on('chunk_text_assets.asset_id', '=', 'asset.id')
				->order_by('position', 'asc')
				->limit(1)
				->where('chunk_text_assets.chunk_id', '=', $chunk->id)
				->where('asset.type', '=', Boom_Asset::IMAGE);

			// If the current user isn't logged in then make sure it's a published asset.
			if ( ! Auth::instance()->logged_in())
			{
				$query->where('asset.visible_from', '<=', Editor::instance()->live_time());
			}

			// Load the result.
			return $this->_thumbnails[$slotname] = $query->find();
		}
	}
}