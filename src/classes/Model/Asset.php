<?php

use \ORM as ORM;

class Model_Asset extends ORM
{
    protected $_belongs_to = [
        'uploader'        =>    ['model' => 'Person', 'foreign_key' => 'uploaded_by'],
        'thumbnail'    =>    ['model' => 'Asset', 'foreign_key' => 'thumbnail_asset_id'],
    ];

    protected $_created_column = [
        'column'    =>    'uploaded_time',
        'format'    =>    true,
    ];

    protected $_has_many = [
        'tags'    => ['model' => 'Tag', 'through' => 'assets_tags'],
    ];

    protected $_table_columns = [
        'id'                =>    '',
        'title'                =>    '',
        'description'        =>    '',
        'width'            =>    '',
        'height'            =>    '',
        'filename'            =>    '',
        'visible_from'        =>    '',
        'type'            =>    '',
        'filesize'            =>    '',
        'duration'            =>    '',
        'uploaded_by'        =>    '',
        'uploaded_time'    =>    '',
        'last_modified'        =>    '',
        'thumbnail_asset_id'    =>    '',
        'credits'            =>    '',
        'downloads'        =>    '',
    ];

    protected $_table_name = 'assets';

    protected $_updated_column = [
        'column'    =>    'last_modified',
        'format'    =>    true,
    ];

    /**
	 *
	 * @var	array	Cache variable for [Model_Asset::getOldFiles()]
	 */
    protected $_old_files = null;

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
