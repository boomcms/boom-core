<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Asset Model
*
* @package	BoomCMS
* @category	Assets
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Model_Asset extends Model_Taggable
{
	protected $_belongs_to = array(
		'uploader'		=>	array('model' => 'Person', 'foreign_key' => 'uploaded_by'),
		'thumbnail'	=>	array('model' => 'Asset', 'foreign_key' => 'thumbnail_asset_id'),
	);

	protected $_created_column = array(
		'column'	=>	'uploaded_time',
		'format'	=>	TRUE,
	);

	protected $_has_many = array(
		'tags'	=> array('model' => 'Tag', 'through' => 'assets_tags'),
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
		'deleted'			=>	FALSE,
		'duration'			=>	'',
		'encoded'			=>	'',
		'views'			=>	'',
		'uploaded_by'		=>	'',
		'uploaded_time'	=>	'',
		'last_modified'		=>	'',
		'thumbnail_asset_id'	=>	'',
	);

	protected $_table_name = 'assets';

	protected $_updated_column = array(
		'column'	=>	'last_modified',
		'format'	=>	TRUE,
	);

	/**
	 *
	 * @var	array	Cache variable for [Model_Asset::old_files()]
	 */
	protected $_old_files = NULL;

	/**
	 * Returns the directory where asset files are stored.
	 *
	 * @return string
	 */
	public function directory()
	{
		return APPPATH.'assets';
	}

	/**
	 * Delete an asset.
	 *
	 * Assets are deleted in two stages:
	 *
	 * * If the deleted property is false then this is changed to true and the asset is merely marked as deleted.
	 * * If the asset is already marked as deleted then the asset is deleted for real.
	 *
	 * An asset which hasn't already been marked as deleted can be deleted entirely by calling with the first paramater set to TRUE.
	 *
	 * @param boolean $force
	 * @uses ORM::delete()
	 *
	 * @return \Boom_Model_Asset
	 */
	public function delete($force = FALSE)
	{
		if ($this->deleted OR $force)
		{
			// Asset is already marked as deleted, so delete it for real.
			return parent::delete();
		}
		else
		{
			// Asset hasn't been marked as deleted yet
			// So only pretend that it's deleted for now.
			return $this
				->set('deleted', TRUE)
				->update();
		}
	}

	public function exists()
	{
		return $this->id AND file_exists($this->get_filename());
	}

	public function filters()
	{
		return array(
			'visible_from' => array(
				array('strtotime'),
			),
		);
	}

	/**
	 * Updates the current object with data from a given file.
	 *
	 * @param string $filepath
	 * @return \Boom_Model_Asset
	 */
	public function get_file_info($filepath)
	{
		// Get the filesize, and type and update the corresponding Model_Asset properties.
		$this->values(array(
			'filesize'		=>	filesize($filepath),
			'type'		=>	Boom_Asset::type_from_mime(File::mime($filepath)),
		));

		// If the asset is an image then set the dimensionis.
		if ($this->type == Boom_Asset::IMAGE)
		{
			// Set the dimensions of the image.
			list($width, $height) = getimagesize($filepath);

			$this
				->set('width', $width)
				->set('height', $height);
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_filename()
	{
		return $this->directory().DIRECTORY_SEPARATOR.$this->id;
	}

	/**
	 * Find the mimetype of the asset file.
	 *
	 * @return string Mimetype string.
	 */
	public function get_mime()
	{
		return File::mime($this->get_filename());
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
			foreach (array_reverse(glob($this->get_filename().".*.bak")) as $file)
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

	public function replace_with_file($filename)
	{
		$this->get_file_info($filename);

		$path = $this->get_filename();
		@rename($path, "{$path}.{$this->last_modified}.bak");
		copy($filename, $path);

		$this->remove_cache_files();

		return $this->update();
	}

	public function remove_cache_files()
	{
		foreach (glob($this->get_filename()."_*.cache") as $file)
		{
			unlink($file);
		}

		return $this;
	}

	/**
	 * Returns the asset's type in a human readable format.
	 *
	 * @return 	string
	 */
	public function type()
	{
		return Boom_Asset::type($this->type);
	}

	/**
	 * Returns an array of the type of assets which exist in the database.
	 *
	 * Retrieves the numeric asset types which are stored in the database.
	 * These are then converted to words using [Boom_Asset::type()]
	 *
	 * @uses Boom_Asset::type()
	 * @return array
	 */
	public function types()
	{
		// Get the available asset types in numeric format.
		$types = DB::select('type')
			->distinct(TRUE)
			->from('assets')
			->where('deleted', '=', FALSE)
			->where('type', '!=', 0)
			->execute($this->_db)
			->as_array();

		// Turn the numeric asset types into user friendly strings.
		$types = Arr::pluck($types, 'type');
		$types = array_map(array('Boom_Asset', 'type'), $types);
		$types = array_map('ucfirst', $types);

		// Return the results.
		return $types;
	}

	/**
	 * Gets an array of the ID and name of people who have uploaded assets.
	 *
	 * The returned array will be an associative array of person ID => name.
	 *
	 * People who have uploaded assets, but who's assets are all deleted, will not appear in the returned array.
	 *
	 * @return array
	 */
	public function uploaders()
	{
		return DB::select('id', 'name')
			->from('people')
			->where('id', 'in', DB::select('uploaded_by')
				->from('assets')
				->where('deleted', '=', FALSE)
				->distinct(TRUE)
			)
			->order_by('name', 'asc')
			->execute($this->_db)
			->as_array('id', 'name');
	}
}
