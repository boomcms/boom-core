<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Text extends Chunk
{
	protected $_type = 'text';

	protected function _add_html($text)
	{
		switch ($this->_slotname)
		{
			case 'standfirst':
				return "<h2 class=\"standFirst\">$text</h2>";
				break;
			case 'bodycopy':
				return "<div id=\"content\">$text</div>";
				break;
			case 'bodycopy2':
				return "<div id=\"content-secondary\">$text</div>";
				break;
			default:
				return "<p>$text</p>";
		}
	}

	protected function _show()
	{
		$text = $this->_chunk->text;
		$text = Text::decode_chunk($text);

		// Embed youtube videos when in site view.
		if ( ! Auth::instance()->logged_in() OR Editor::state() != Editor::EDIT)
		{
			$text = Text::auto_link_video($text);
		}

		// If no template has been set then add the default HTML tags for this slotname.
		if ($this->_template === NULL)
		{
			return $this->_add_html($text);
		}
		else
		{
			return View::factory("site/slots/text/$this->_template", array('text' => $text));
		}
	}

	protected function _show_default()
	{
		$text = __(Kohana::message('chunks', 'text'));

		// Add a <p> tag around the default text for bodycopies.
		// This needs to be done a bit better. Perhaps use default temlates instead of _add_htm()?
		if ($this->_slotname == 'bodycopy' OR $this->_slotname == 'bodycopy2')
		{
			$text = "<p>$text</p>";
		}

		if ($this->_template === NULL)
		{
			return $this->_add_html($text);
		}
		else
		{
			return View::factory("site/slots/text/$this->_template", array('text' => $text));
		}
	}

	public function has_content()
	{
		return $this->_chunk->text != NULL;
	}

	/**
	* Returns the text from the chunk.
	*/
	public function text()
	{
		return $this->_chunk->text;
	}
}