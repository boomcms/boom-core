<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Feature extends Chunk
{
	/**
	* holds the page which is being featured.
	* @var Model_Page
	*/
	protected $_target_page;
	
	protected $_type = 'feature';

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

	public function target()
	{
		return $this->target_page()->pk();
	}

	/**
	* Returns the target page of the feature.
	*
	* @return Model_Page
	*/
	public function target_page()
	{
		if ($this->_target_page === NULL)
		{
			$this->_target_page = ORM::factory('Page', $this->_chunk->target_page_id);
		}

		return $this->_target_page;
	}
}