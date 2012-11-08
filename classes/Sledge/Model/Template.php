<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Template extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_one = array(
		'page'	=>	array('model' => 'Page_Versions', 'foreign_key' => 'id'),
	);
	protected $_table_columns = array(
		'id'			=>	'',
		'name'		=>	'',
		'description'	=>	'',
		'filename'		=>	'',
		'visible'		=>	'',
	);

	/**
	* Determines whether the template file exists.
	*
	* @return boolean
	* @todo This should use Kohana::find_file();
	*/
	public function file_exists()
	{
		return (bool) Kohana::find_file("views", Sledge::TEMPLATE_DIR . $this->filename);
	}

	/**
	* Returns a count of the number of the pages which use a template.
	*
	* @return int
	*/
	public function page_count()
	{
		if ( ! $this->loaded())
		{
			return 0;
		}

		$query = DB::select(array(DB::expr('count(*)'), 'pages'))
			->from('pages')
			->join('page_versions', 'inner')
			->on('pages.active_vid', '=', 'page_versions.id')
			->where('template_id', '=', $this->id)
			->where('deleted', '=', FALSE)
			->execute()
			->as_array();

		return $query[0]['pages'];
	}

	/**
	* ORM Validation rules
	* @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
			'filename' => array(
				array('not_empty'),
			),
		);
	}
}
