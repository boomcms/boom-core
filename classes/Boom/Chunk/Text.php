<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package	BoomCMS
 * @category	Chunks
 *
 */
class Boom_Chunk_Text extends Chunk
{
	protected $_type = 'text';

	protected function _add_html($text)
	{
		$title = " title='".$this->_chunk->title."'";

		switch ($this->_chunk->slotname)
		{
			case 'standfirst':
				return "<p class=\"standFirst\"$title>$text</p>";
			case 'bodycopy':
				return "<div id=\"content\"$title>$text</div>";
			case 'bodycopy2':
				return "<div id=\"content-secondary\"$title>$text</div>";
			default:
				return "<p$title>$text</p>";
		}
	}

	/**
	 *
	 * @uses Model_Chunk_Text::unmunge()
	 * @uses Chunk_Text::embed_video()
	 */
	protected function _show()
	{
		$text = $this->text();

		// If no template has been set then add the default HTML tags for this slotname.
		if ($this->_template === null)
		{
			return $this->_add_html($text);
		}
		else
		{
			return View::factory($this->_view_directory."text/$this->_template", array('text' => $text, 'title' => $this->_chunk->title, 'chunk' => $this->_chunk));
		}
	}

	protected function _show_default()
	{
		$text = Kohana::message('chunks', $this->_slotname);
		$text || $text = Kohana::message('chunks', 'text');

		$template = ($this->_template === null)? $this->_slotname : $this->_template;

		if ( ! Kohana::find_file('views', $this->_view_directory."text/$template"))
		{
			return "<p>$text</p>";
		}
		else
		{
			return View::factory($this->_view_directory."text/$template", array(
				'text'	=>	$text,
				'title'	=>	Kohana::message('chunks', 'title'),
				'chunk' => $this->_chunk,
			));
		}
	}

	public function get_paragraphs($offset = 0, $length = null)
	{
		preg_match_all('|<p>(.*?)</p>|', $this->_chunk->text, $matches);
		$paragraphs = $matches[0];

		return array_slice($paragraphs, $offset, $length);
	}

	public function has_content()
	{
		return trim($this->_chunk->text) != null;
	}

	public function text()
	{
		$text = \Boom\Editor::instance()->isEnabled()? $this->_chunk->text : $this->_chunk->site_text;

		$text = html_entity_decode($text);
		$text = $this->_chunk->unmunge($text);

		return $text;
	}
}