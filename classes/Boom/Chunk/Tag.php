<?php defined('SYSPATH') OR die('No direct script access.');

/**
* @package	BoomCMS
* @category	Chunks
*
*/
class Boom_Chunk_Tag extends Chunk
{
	protected $_default_template = 'gallery';
	protected $_tag;
	protected $_type = 'tag';

	public function __construct(Model_Page $page, $chunk, $editable = TRUE)
	{
		parent::__construct($page, $chunk, $editable);

		$this->_tag = $this->_chunk->target;
	}

	protected function _show()
	{
		if ( ! $this->_template OR ! Kohana::find_file("views", $this->_view_directory."tag/$this->_template"))
		{
			$this->_template = $this->_default_template;
		}

		return View::factory($this->_view_directory."tag/$this->_template", array(
			'tag' => $this->_tag,
		));
	}

	protected function _show_default()
	{
		return new View($this->_view_directory."default/tag/$this->_template");
	}

	public function attributes()
	{
		return array(
			$this->_attribute_prefix.'tag_id' => $this->get_tag()->id,
		);
	}

	public function get_tag()
	{
		return $this->_tag;
	}

	public function has_content()
	{
		return $this->_chunk->loaded() AND $this->get_tag()->loaded();
	}
}