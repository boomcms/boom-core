<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 *
 */
class Boom_Model_Chunk_Timestamp extends ORM
{
	protected $_table_columns = array(
		'id' => '',
		'timestamp' => '',
		'format' => '',
		'slotname'	=> '',
	);

	protected $_table_name = 'chunk_timestamps';

	public function is_valid_format()
	{
		return in_array($this->format, Chunk_Timestamp::$formats);
	}

	public function filters()
	{
		return array(
			'timstamp' => array(
				array('strtotime'),
			),
		);
	}

	public function rules()
	{
		return array(
			'timestamp' => array(
				array(array($this, 'is_valid_format'))
			),
		);
	}
}