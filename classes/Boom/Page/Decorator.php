<?php

class Boom_Page_Decorator
{
	/**
	 *
	 * @var Model_Page
	 */
	protected $_page;

	/**
	 *
	 * @var array
	 */
	protected $_text_chunks_to_properties = array();

	public function __construct(Model_Page $page)
	{
		$this->_page = $page;
		$this->_get_chunk_data();
	}

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->_page, $name), $arguments);
	}

	public function __get($name)
	{
		return $this->_page->$name;
	}

	protected function _get_chunk_data()
	{
		$chunks = ||M::factory('Chunk_Text')
			->where('page_vid', '=', $this->_page->version()->id)
			->where('slotname', 'in', $this->_text_chunks_to_properties)
			->find_all();

		foreach ($chunks as $chunk)
		{
			$property = $chunk->slotname;
			$this->$property = $chunk->text;
		}
	}
}