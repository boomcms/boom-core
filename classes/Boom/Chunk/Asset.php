<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Chunk_Asset extends Chunk
{
	protected $_asset;

	protected $_default_template = 'image';

	protected $_type = 'asset';

	public function __construct(Model_Page $page, $chunk, $editable = TRUE)
	{
		parent::__construct($page, $chunk, $editable);

		$this->_asset = $this->_chunk->target;
	}

	protected function _show()
	{
		$v = View::factory("site/slots/asset/$this->_template");

		// If the URL is just a number then assume it's the page ID for an internal link.
		if (preg_match('/^\d+$/D', $this->_chunk->url))
		{
			$target = new Model_Page($this->_chunk->url);
			$v->title = $target->version()->title;
			$v->url = $target->url();
		}
		else
		{
			$v->title = $this->_chunk->title;
			$v->url = $this->_chunk->url;
		}

		$v->asset = $this->_chunk->target;
		$v->caption = $this->_chunk->caption;

		return $v;
	}

	protected function _show_default()
	{
		return View::factory("site/slots/default/asset/$this->_template");
	}

	/**
	 * Adds a target asset ID data attribute.
	 *
	 */
	public function add_attributes($html, $type, $slotname, $template, $page_id)
	{
		$html = parent::add_attributes($html, $type, $slotname, $template, $page_id);

		return preg_replace("|<(.*?)>|", "<$1 data-boom-target='".$this->target()."'>", $html, 1);
	}

	public function asset()
	{
		return $this->_asset;
	}

	public function has_content()
	{
		return $this->_asset->loaded();
	}

	public function target()
	{
		return $this->_asset->id;
	}
}