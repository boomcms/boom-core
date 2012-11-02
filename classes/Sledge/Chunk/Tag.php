<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Tag extends Chunk
{
	protected $_type = 'tag';

	protected function _show()
	{
		$parent_tag = $this->chunk()->tag_id;
		$tag = Arr::get(Request::initial()->param('options'), 0, Arr::get(Request::initial()->post(), 'tag', $parent_tag));

		return Request::factory('sledge/asset/library')
				->post( array('parent_tag' => $parent_tag, 'tag' => $tag))
				->execute();
	}

	protected function _show_default()
	{
	}

	/**
	* The target for a tag chunk is the ID of the tag that's assigned to it.
	*
	* @return int
	*/
	public function target()
	{
		return $this->_chunk->tag_id;
	}
}