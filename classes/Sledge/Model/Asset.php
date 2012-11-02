<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Asset Model
*
* @see	[Model_Asset_Version]
* @package	Sledge
* @category	Assets
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Asset extends ORM_Taggable
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array(
		'version'  => array('model' => 'Asset_Version', 'foreign_key' => 'active_vid'),
	);
	protected $_has_many = array(
		'revisions'	=>	array('model' => 'Asset_Version', 'foreign_key' => 'rid'),
	);

	protected $_load_with = array('version');

	protected $_table_columns = array(
		'id'			=>	'',
		'active_vid'	=>	'',
	);

	/**
	* Value for asset status published.
	* @var integer
	*/
	const STATUS_UNPUBLISHED = 1;

	/**
	* Value for asset status published.
	* @var integer
	*/
	const STATUS_PUBLISHED = 2;

	/**
	 *
	 * @var	array	Cache variable for [Model_Asset::old_files()]
	 */
	protected $_old_files = NULL;

	/**
	* Array of tags which have been applied to the current asset.
	* @see self::tags()
	* @access private
	* @var array
	*/
	private $_tags;

	/**
	 * Returns an array of old files which have been replaced.
	 * Where an asset has been replaced the array will contain the names of the backup files for the previous versions.
	 *
	 * @return	array
	 */
	public function old_files()
	{
		// If the asset doesn't exist return an empty array.
		if ( ! $this->loaded())
		{
			return array();
		}

		if ($this->_old_files === NULL)
		{
			// Add files for previous versions of the asset.
			// Wrap the glob in array_reverse() so that we end up with an array with the most recent first.
			foreach (array_reverse(glob(ASSETPATH . $this->id . ".*.bak")) as $file)
			{
				// Get the version ID out of the filename.
				preg_match('/' . $this->id . '.(\d+).bak$/', $file, $matches);

				if (isset($matches[1]))
				{
					$this->_old_files[$matches[1]] = $file;
				}
				else
				{
					$this->_old_files[] = $file;
				}
			}
		}

		return $this->_old_files;
	}

	/**
	* Returns a human readable asset status (currently published or unpublished).
	*
	* @return string Asset status
	*/
	public function get_status()
	{
		switch ($this->status)
		{
			case self::STATUS_PUBLISHED:
				return 'Published';
				break;
			case self::STATUS_UNPUBLISHED:
				return 'Unpublished';
				break;
			default:
				throw new Kohana_Exception('Asset has unknown asset status value: ' . $this->status);
		}
	}

	/**
	* Apply a tag to the current asset.
	* Required by the taggable interface.
	* Creates a relationship with the tag table in the tags_applied table.
	*
	* @uses self::$_tags
	* @usesModel_Tag_Applied
	* @param Model_Tag $tag The tag to be applied.
	* @return bool True on success, FALSE on failure
	*/
	public function apply_tag(Model_Tag $tag)
	{
		$values = array(
			'tag_id'		=> $tag->pk(),
			'object_type'	=>Model_Tag_Applied::OBJECT_TYPE_ASSET,
			'object_id'		=> $this->pk()
		);

		try
		{
			$tagged = ORM::factory('Tagged_object')->values($values)->create();
		}
		catch (DatabaseException $e)
		{
			return FALSE;
		}

		// Add the new relationship to self::$_tags if it's been loaded.
		if (is_array($this->_tags))
		{
			$this->_tags = array_push($this->_tags, $tag);
		}
	}

	/**
	* Find the mimetype of the asset file.
	*
	* @return string Mimetype string.
	*/
	public function get_mime()
	{
		return File::mime(ASSETPATH . $this->id);
	}

	/**
	* Similar to count_all() but this sums the filesize column of the current query.
	*/
	public function size_all()
	{
		$selects = array();

		foreach ($this->_db_pending as $key => $method)
		{
		    if ($method['name'] == 'select')
		    {
		        // Ignore any selected columns for now
		        $selects[] = $method;
		        unset($this->_db_pending[$key]);
		    }
		}

		if ( ! empty($this->_load_with))
		{
		    foreach ($this->_load_with as $alias)
		    {
		        // Bind relationship
		        $this->with($alias);
		    }
		}

		$this->_build(Database::SELECT);

		$filesize = $this->_db_builder->from(array($this->_table_name, $this->_object_name))
		    ->select(array(DB::expr('sum("filesize")'), 'filesize'))
		    ->execute($this->_db)
		    ->get('filesize');

		// Add back in selected columns
		$this->_db_pending += $selects;

		$this->reset();

		// Return the total filesize of the assets.
		return $filesize;
	}
}
