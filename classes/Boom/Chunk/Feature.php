<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Chunk_Feature extends Chunk
{
	/**
	* holds the page which is being featured.
	* @var Model_Page
	*/
	protected $_target_page;

	protected $_type = 'feature';

	public function __construct(Model_Page $page, $chunk, $editable = TRUE)
	{
		parent::__construct($page, $chunk, $editable);

		$this->_target_page = new Model_Page($this->_chunk->target_page_id);
	}

	/**
	* Show a chunk where the target is set.
	*/
	public function _show()
	{
		// If the template doesn't exist then use a default template.
		if ( ! Kohana::find_file("views", "site/slots/feature/$this->_template"))
		{
			$this->_template = $this->_default_template;
		}

		$v = View::factory("site/slots/feature/$this->_template");
		$v->target = $this->target_page();

		return $v;
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/feature/$this->_template");
	}

	public function has_content()
	{
		return $this->_target_page->loaded();
	}

	public function target()
	{
		return $this->_target_page->id;
	}

	/**
	* Returns the target page of the feature.
	*
	* @return Model_Page
	*/
	public function target_page()
	{
		return $this->_target_page;
	}
}