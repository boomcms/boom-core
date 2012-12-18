<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	Sledge
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Chunk_Text extends Chunk
{
	protected $_type = 'text';

	/**
	 * Embed HTML for a YouTube video.
	 *
	 * @var string
	 */
	public static $youtube_embed = "<iframe width=\"560\" height=\"315\" src=\"http://www.youtube.com/embed/:video_id\" frameborder=\"0\" allowfullscreen></iframe>";

	/**
	 * Embed HTML for a Vimeo video.
	 *
	 * @var string
	 */
	public static $vimeo_embed = "<iframe src='http://player.vimeo.com/video/:video_id' width='500' height='281' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";

	protected function _add_html($text)
	{
		$title = " title='".$this->_chunk->title."'";

		switch ($this->_chunk->slotname)
		{
			case 'standfirst':
				return "<h2 class=\"standFirst\"$title>$text</h2>";
				break;
			case 'bodycopy':
				return "<div id=\"content\"$title>$text</div>";
				break;
			case 'bodycopy2':
				return "<div id=\"content-secondary\"$title>$text</div>";
				break;
			default:
				return "<p$title>$text</p>";
		}
	}

	/**
	 *
	 * @uses Chunk_Text::unmunge()
	 * @uses Chunk_Text::embed_video()
	 */
	protected function _show()
	{
		$text = $this->_chunk->text;
		$text = Chunk_Text::unmunge($text);

		// Embed youtube videos when in site view.
		if (Editor::instance()->state() != Editor::EDIT)
		{
			// This mammoth regular expression matches a URL
			//  Rob didn't write this, I'm not smart enough for this.
			// See http://daringfireball.net/2010/07/improved_regex_for_matching_urls for an explanation if curious.
			$text = preg_replace_callback('~(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))~', 'Chunk_Text::embed_video', $text);
		}

		// If no template has been set then add the default HTML tags for this slotname.
		if ($this->_template === NULL)
		{
			return $this->_add_html($text);
		}
		else
		{
			return View::factory("site/slots/text/$this->_template", array('text' => $text, 'title' => $this->_chunk->title));
		}
	}

	protected function _show_default()
	{
		$text = __(Kohana::message('chunks', 'text'));

		// Add a <p> tag around the default text for bodycopies.
		// This needs to be done a bit better. Perhaps use default temlates instead of _add_htm()?
		if ($this->_chunk->slotname == 'bodycopy' OR $this->_chunk->slotname == 'bodycopy2')
		{
			$text = "<p>$text</p>";
		}

		if ($this->_template === NULL)
		{
			return $this->_add_html($text);
		}
		else
		{
			return View::factory("site/slots/text/$this->_template", array(
				'text'	=>	$text,
				'title'	=>	$this->_chunk->title,
			));
		}
	}

	/**
	 * Auto-embed a video from a video sharing site.
	 * Turns a link to a youtube (vimeo, etc.) page into an embedded video.
	 *
	 * @param	string	$text		The URL of a video to turn into an embedded video.
	 * @return 	string
	 */
	public static function embed_video($url)
	{
		// Check for a scheme at the start of the URL, add it if necessary.
		if (substr($url, 0, 4) != 'http')
		{
			$url = 'http://' . $url;
		}

		$url = parse_url($url);

		if (strpos($url['host'], 'youtube') !== FALSE AND isset($url['query']))
		{
			// Youtube video long format.
			parse_str($url['query']);

			if (isset($v))
			{
				$video_id = $v;
				$embed_html = Chunk_Text::$youtube_embed;
			}
		}
		elseif ($url['host'] == 'youtu.be')
		{
			// Youtube video short link.
			$video_id = str_replace("/", "", $url['path']);
			$embed_html = Chunk_Text::$youtube_embed;
		}
		elseif (strpos($url['host'], 'vimeo') !== FALSE AND isset($url['path']))
		{
			// Vimeo video
			$video_id = str_replace("/", "", $url['path']);
			$embed_html = Chunk_Text::$vimeo_embed;
		}

		if (isset($video_id))
		{
			return str_replace(':video_id', $video_id, $embed_html);
		}
	}

	public function has_content()
	{
		return $this->_chunk->text != NULL;
	}

	/**
	 * Munges text chunk contents to be saved in the database.
	 * e.g. Turns text links, such as <img src='/asset/view/324'> in hoopdb:// links
	 *
	 * @param 	string	$text		Text to munge
	 * @return 	string
	 */
	public static function munge($text)
	{
		return preg_replace('|<(.*?)src=[\'"]/asset/view(\d+)(.*?)[\'"](.*)>|', '<$1 src="hoopdb://image/$2"$4>', $text);
	}

	/**
	 * Returns the text from the chunk.
	 */
	public function text()
	{
		return $this->_chunk->text;
	}

	/**
	 * Turns text chunk contents into HTML.
	 * e.g. replaces hoopdb:// links to <img> and <a> links
	 *
	 * @param	string	$text	Text to decode
	 * @return 	string
	 */
	public static function unmunge($text)
	{
		// Image links in the form hoopdb://image/123
		$text = preg_replace('|hoopdb://image/(\d+)|', '/asset/view/$1/400', $text);

		// Fix internal page links.
		$text = preg_replace_callback('|hoopdb://page/(\d+)|',
			function ($match)
			{
				return ORM::factory('Page', $match[1])
					->link();
			},
			$text
		);

		return $text;
	}
}