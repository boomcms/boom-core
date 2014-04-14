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
		'duration'			=>	'',
		'uploaded_by'		=>	'',
		'uploaded_time'	=>	'',
		'last_modified'		=>	'',
		'thumbnail_asset_id'	=>	'',
		'credits'			=>	'',
		'downloads'		=>	'',
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

	public function create_from_file($filepath, $keep_original = TRUE)
	{
		// Update the model with details about hte file.
		$this->get_file_info($filepath);

		// Persist the asset data so that we can get an asset ID.
		$this->create();

		// If we're keeping the original file then copy() the original, otherwise rename() it.
		$command = ($keep_original)? 'copy' : 'rename';

		try
		{
			// Copy / move the file into the assets directory.
			$command($filepath, $this->directory().DIRECTORY_SEPARATOR.$this->id);
		}
		catch (Exception $e)
		{
			// We couldn't get the asset into the assets directory.
			// So that we don't end up with an asset that doesn't have a file we'll just delete the asset data.
			$this->delete(TRUE);

			// Throw the exception, it's someone elses problem now.
			throw $e;
		}

		// Return the current model.
		return $this;
	}

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
	 * @return \Boom_Model_Asset
	 */
	public function delete()
	{
		$this->delete_files();

		return parent::delete();
	}

	public function delete_cache_files()
	{
		foreach (glob($this->get_filename()."_*.cache") as $file)
		{
			unlink($file);
		}

		return $this;
	}

	public function delete_files()
	{
		$this
			->delete_cache_files()
			->delete_old_versions();

		@unlink($this->get_filename());
	}

	public function delete_old_versions()
	{
		foreach (glob($this->get_filename().".*.bak") as $file)
		{
			unlink($file);
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

	public function get_aspect_ratio()
	{
		return ($this->height > 0)? ($this->width / $this->height) : 1;
	}

	public function get_extension()
	{
		return Boom_Asset::extension_from_mime($this->get_mime());
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
		return $this->exists()? File::mime($this->get_filename()) : NULL;
	}

	public function is_visible()
	{
		return $this->visible_from < $_SERVER['REQUEST_TIME'];
	}

	public function log_download($ip)
	{
		$ip = ip2long($ip);

		$logged = DB::select(DB::expr("1"))
			->from('asset_downloads')
			->where('ip', '=', $ip)
			->where('asset_id', '=', $this->id)
			->where('time', '>=', time() - Date::MINUTE * 10)
			->limit(1)
			->execute()
			->as_array();

		if ( ! count($logged))
		{
			ORM::factory('Asset_Download')
				->values(array(
					'asset_id' => $this->id,
					'ip' => $ip,
				))
				->create();

			DB::update($this->_table_name)
				->set(array('downloads' => DB::expr('downloads + 1')))
				->where('id', '=', $this->id)
				->execute($this->_db);
		}

		return $this;
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

		$this->delete_cache_files();

		return $this->update();
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
}