<?php

namespace Boom\Model;

use \Arr as Arr;
use \DB as DB;
use \ORM as ORM;

class Asset extends Taggable
{
	protected $_belongs_to = array(
		'uploader'		=>	array('model' => 'Person', 'foreign_key' => 'uploaded_by'),
		'thumbnail'	=>	array('model' => 'Asset', 'foreign_key' => 'thumbnail_asset_id'),
	);

	protected $_created_column = array(
		'column'	=>	'uploaded_time',
		'format'	=>	true,
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
		'format'	=>	true,
	);

	/**
	 *
	 * @var	array	Cache variable for [Model_Asset::getOldFiles()]
	 */
	protected $_old_files = null;

	public function create_from_file($filepath, $keep_original = true)
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
			$command($filepath, \Boom\Asset::directory().DIRECTORY_SEPARATOR.$this->id);
		}
		catch (Exception $e)
		{
			// We couldn't get the asset into the assets directory.
			// So that we don't end up with an asset that doesn't have a file we'll just delete the asset data.
			$this->delete(true);

			// Throw the exception, it's someone elses problem now.
			throw $e;
		}

		// Return the current model.
		return $this;
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
			'filesize'		=> filesize($filepath),
			'type'		=> \Boom\Asset\Mimetype::factory(\File::mime($filepath))->getType(),
		));

		// If the asset is an image then set the dimensionis.
		if ($this->type == \Boom\Asset\Type::IMAGE)
		{
			// Set the dimensions of the image.
			list($width, $height) = getimagesize($filepath);

			$this
				->set('width', $width)
				->set('height', $height);
		}

		return $this;
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

	public function replace_with_file($filename)
	{
		$this->get_file_info($filename);

		$path = $this->getFilename();
		@rename($path, "{$path}.{$this->last_modified}.bak");
		copy($filename, $path);

		$this->delete_cache_files();

		return $this->update();
	}

	/**
	 * Returns an array of the type of assets which exist in the database.
	 *
	 * Retrieves the numeric asset types which are stored in the database.
	 * These are then converted to words using [\Boom\Asset\Type::type()]
	 *
	 * @uses \Boom\Asset\Type::type()
	 * @return array
	 */
	public function types()
	{
		// Get the available asset types in numeric format.
		$types = DB::select('type')
			->distinct(true)
			->from('assets')
			->where('type', '!=', 0)
			->execute($this->_db)
			->as_array();

		// Turn the numeric asset types into user friendly strings.
		$types = Arr::pluck($types, 'type');
//		$types = array_map(array('\Boom\Asset', 'type'), $types);
//		$types = array_map('ucfirst', $types);

		// Return the results.
		return $types;
	}
}