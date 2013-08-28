<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
*
*/
class Boom_Chunk_Timestamp extends Chunk
{
	public static $default_format = 'j F Y';
	public static $formats = array(
		'j F Y',
	);

	protected $_html_before = "<span class='b-chunk-timestamp'>";
	protected $_html_after = "</span>";
	protected $_type = 'timestamp';

	public function add_attributes($html, $type, $slotname, $template, $page_id)
	{
		$html = parent::add_attributes($html, $type, $slotname, $template, $page_id);

		return preg_replace("|<(.*?)>|", "<$1 data-boom-timestamp='".$this->_chunk->timestamp."' data-boom-format='".$this->_chunk->format."'>", $html, 1);
	}

	protected function _show()
	{
		return $this->_show_timestamp($this->_chunk->format, $this->_chunk->timestamp);
	}

	protected function _show_default()
	{
		return $this->_show_timestamp(static::$default_format, time());
	}

	protected function _show_timestamp($format, $timestamp)
	{
		return $this->_html_before.date($format, $timestamp).$this->_html_after;
	}

	public function has_content()
	{
		return $this->_chunk->timestamp > 0;
	}

	public function timestamp()
	{
		return $this->_chunk->timestamp;
	}
}