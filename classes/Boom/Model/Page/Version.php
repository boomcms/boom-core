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
		'feature_image' => array('model' => 'Asset', 'foreign_key' => 'feature_image_id')
	);

	protected $_created_column = array(
		'column'	=>	'edited_time',
		'format'	=>	true,
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
		'pending_approval'	=>	'',
	);

	protected $_table_name = 'page_versions';

	/**
	 * Hold the calculated thumbnails for this version.
	 *
	 * @see Model_Page_Version::thumbnail()
	 * @var array
	 */
	protected $_thumbnails = array();

	/**
	 * Adds a chunk to the page version.
	 *
	 * This should only be called when the page version has been saved and therefore has a version ID.
	 *
	 * This function assumes that the specified chunk doesn't already exist for the page version.
	 * I can't think of a situation where we'd ever be updating a chunk which has already been added to a page version.
	 * If we want to update a chunk on a page then we would create a new version and add the chunk to the latest version.
	 * Checking whether a chunk exists and then updating it if necessary would therefore add extra DB queries with little benefit.
	 *
	 * **Examples**
	 *
	 * Add a text chunk to a version:
	 *
	 *		$version->add_chunk('text', 'standfirst', array('text' => 'Some text'));
	 *		$version->add_chunk('text', 'standfirst', array('text' => 'Some text', 'title' => 'A text chunk with a title'));
	 *
	 * Add a feature chunk to a version:
	 *
	 *		$version->add_chunk('feature', 'feature_box_1', array('target_page_id' => 1));
	 *
	 * @param	string	$type	The type of chunk to add, e.g. text, feature, etc.
	 * @param	string	$slotname	The slotname of the chunk
	 * @param	array	$data	Array of values to assign to the new chunk.
	 * @return	Model	Returns the model object for the created chunk
	 * @throws	Exception	An exception is thrown when this function is called on a page version which hasn't been saved.
	 *
	 */
	public function add_chunk($type, $slotname, array $data)
	{
		if ( ! ($this->_saved OR $this->_loaded))
		{
			throw new Exception('You must call Model_Page_Version::save() before calling Model_Page_Version::add_chunk()');
		}

		$data['slotname'] = $slotname;
		$data['page_vid'] = $this->id;

		$chunk = ORM::factory('Chunk_' . ucfirst($type))
			->values($data)
			->create();

		return $chunk;
	}

	/**
	 * Copies the chunks from another page version to this version.
	 *
	 * @param Model_Page_Version $from_version
	 * @param array $exclude An array of slotnames which shouldn't be copied from the other version.
	 * @return Model_Page_Version
	 */
	public function copy_chunks(Model_Page_Version $from_version, array $exclude = NULL)
	{
		$copier = new Page_ChunkCopier($from_version, $this, $exclude);
		$copier->copy_all();

		return $this;
	}

	/**
	 * Embargoes the page version until the specified time.
	 *
	 * @param int	$time	Unix timestamp
	 * @return Model_Page_Version
	 */
	public function embargo($time)
	{
		// Set any previous embargoed versions to unpublished to ensure that they won't be used.
		DB::update('page_versions')
			->set(array(
				'published'	=>	FALSE,
			))
			->where('embargoed_until', '>', $_SERVER['REQUEST_TIME'])
			->where('page_id', '=', $this->page_id)
			->where('id', '!=', $this->id)
			->execute($this->_db);

		// Updated the embargo time of the new version.
		$this
			->set('published', true)
			->set('embargoed_until', $time)
			->save();

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
				array('strip_tags'),
				array('html_entity_decode'),
				array('trim'),
				array(
					function($text)
					{
						return str_replace('&nbsp;', ' ', $text);
					}
				),
			),
			'keywords' => array(
				array('trim'),
			),
			'description' => array(
				array('trim'),
			),
	   );
	}

	public function is_published()
	{
		return $this->embargoed_until AND $this->embargoed_until < $_SERVER['REQUEST_TIME'];
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
				array('max_length', array(':value', 70))
			),
		);
	}

	/**
	 * Returns the status of the current page version.
	 *
	 * Status could be:
	 *
	 * * 'published' if the version is published.
	 * * 'embargoed' if the version is published but won't become live until a future time.
	 * * 'draft' if it's not published.
	 *
	 * @return string
	 */
	public function status()
	{
		if ($this->pending_approval)
		{
			return 'pending approval';
		}
		elseif ($this->embargoed_until === NULL)
		{
			// Version is a draft if an embargo time hasn't been set.
			return 'draft';
		}
		elseif ($this->embargoed_until <= Editor::instance()->live_time())
		{
			// Version is live if the embargo time is in the past.
			return 'published';
		}
		elseif ($this->embargoed_until > Editor::instance()->live_time())
		{
			// Version is embargoed if the embargo time is in the future.
			return 'embargoed';
		}
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
