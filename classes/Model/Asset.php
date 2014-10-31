<?php

use \Arr as Arr;
use \DB as DB;
use \ORM as ORM;

class Model_Asset extends Model_Taggable
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
}