<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Model_Page_Version extends ORM
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

	protected $_cache_columns = array('internal_name');

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
	 * Import the chunks from another version to this version.
	 *
	 * @param Model_Page_Version $from_version
	 * @param array $exclude An array of slotnames which shouldn't be copied from the other version.
	 * @return Model_Page_Version
	 */
	public function import_chunks(Model_Page_Version $from_version, array $exclude)
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

			$x = DB::insert('page_chunks', array('page_vid', 'chunk_id'))
				->select($subquery)
				->execute();
		}

		return $this;
	}
}