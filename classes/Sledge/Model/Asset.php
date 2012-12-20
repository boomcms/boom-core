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
		'uploader'	=>	array('model' => 'person', 'foreign_key' => 'uploaded_by'),
	);

	protected $_created_column = array(
		'column'	=>	'uploaded_by',
		'format'	=>	TRUE,
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'title'				=>	'',
		'description'		=>	'',
		'width'			=>	'',
		'height'			=>	'',
		'filename'			=>	'',
		'visible_from'		=>	'',
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

	protected $_table_name = 'assets';

	protected $_updated_column = array(
		'column'	=>	'uploaded_time',
		'format'	=>	TRUE,
	);

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
	 * Find the mimetype of the asset file.
	 *
	 * @return string Mimetype string.
	 */
	public function get_mime()
	{
		return File::mime(ASSETPATH . $this->id);
	}
}
