<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Asset Model
*
* @package	Sledge
* @category	Assets
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Asset extends ORM_Taggable
{
	protected $_belongs_to = array(
		'uploader'	=>	array('model'	=>	'person', 'foreign_key' => 'uploaded_by'),
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'title'				=>	'',
		'description'		=>	'',
		'width'			=>	'',
		'height'			=>	'',
		'filename'			=>	'',
		'visible_from'		=>	'',
		'status'			=>	'',
		'type'			=>	'',
		'filesize'			=>	'',
		'rubbish'			=>	FALSE,
		'duration'			=>	'',
		'encoded'			=>	'',
		'views'			=>	'',
		'uploaded_by'		=>	'',
		'uploaded_time'	=>	'',
		'last_modified'		=>	'',
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
	* Returns a human readable asset type.
	*/
	public function get_type()
	{
		return Sledge_Asset::get_type($this->type);
	}

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
	* Find the mimetype of the asset file.
	*
	* @return string Mimetype string.
	*/
	public function get_mime()
	{
		return File::mime(ASSETPATH . $this->id);
	}
}
