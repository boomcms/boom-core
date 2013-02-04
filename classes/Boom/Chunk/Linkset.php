<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Chunk_Linkset extends Chunk
{
	protected $_default_template = 'quicklinks';

	protected $_type = 'linkset';

	protected function _show()
	{
		if ( ! Editor::instance()->state_is(Editor::DISABLED))
		{
			// Editor is enabled, show all the links.
			$links = $this->_chunk->links();
		}
		else
		{
			// Editor is disabled - only show links where the target page is visible
			$links = array();

			foreach ($this->_chunk->links() as $link)
			{
				if ($link->is_external() OR $link->target->is_visible())
				{
					$links[] = $link;
				}
			}
		}

		return View::factory("site/slots/linkset/$this->_template", array(
			'title'		=>	$this->_chunk->title,
			'links'	=>	$links,
		));
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/linkset/$this->_template");
	}

	public function has_content()
	{
		return count($this->_chunk->links()) > 0;
	}
}