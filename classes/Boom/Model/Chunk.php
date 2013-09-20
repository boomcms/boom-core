<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_Model_Chunk extends ORM
{
	public function copy()
	{
		return ORM::factory($this->_object_name)
			->values($this->object());
	}
}