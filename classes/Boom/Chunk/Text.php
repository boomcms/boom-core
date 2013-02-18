<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Chunk_Text extends Chunk
{
	/**
	 * The height to use when embedding youtube or vimeo videos.
	 *
	 * Can be set by passing a new value to [Chunk_Text::embed_height()]
	 *
	 * @var integer
	 */
	protected $_embed_height = 315;

	/**
	 * The width to use when embedding youtube or vimeo videos.
	 *
	 * Can be set by passing a new value to [Chunk_Text::embed_width()]
	 *
	 * @var integer
	 */
	protected $_embed_width = 560;

	protected $_type = 'text';

	/**
	 * Embed HTML for a YouTube video.
	 *
	 * @var string
	 */
	public static $youtube_embed = "<iframe width=\:width\" height=\":height\" src=\"http://www.youtube.com/embed/:video_id\" frameborder=\"0\" allowfullscreen></iframe>";

	/**
	 * Embed HTML for a Vimeo video.
	 *
	 * @var string
	 */
	public static $vimeo_embed = "<iframe width=':width' height=':height' src='http://player.vimeo.com/video/:video_id' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";

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
		$text = html_entity_decode($this->_chunk->text);

		// Embed youtube videos when in site view.
		if (Editor::instance()->state() != Editor::EDIT)
		{
			$text = preg_replace_callback('~\b(?<!href="|">)(?:ht|f)tps?://[^<\s]+(?:/|\b)~i', array($this, 'embed_video'), $text);
		}

		$text = Chunk_Text::unmunge($text);

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
		$template = ($this->_template === NULL)? $this->_slotname : $this->_template;

		if ( ! Kohana::find_file('views', "site/slots/text/$template"))
		{
			return "<p>$text</p>";
		}
		else
		{
			return View::factory("site/slots/text/$template", array(
				'text'	=>	$text,
				'title'	=>	$this->_chunk->title,
			));
		}
	}

	/**
	 * Set the iframe height for embedded flash videos.
	 *
	 * @param integer
	 * @return Chunk_Text
	 */
	public function embed_height($height)
	{
		// Set the new height.
		$this->_embed_height = $height;

		// Return the current text chunk.
		return $this;
	}

	/**
	 * Auto-embed a video from a video sharing site.
	 *
	 * Turns a link to a youtube (vimeo, etc.) page into an embedded video.
	 *
	 * @param	string	$text		The URL of a video to turn into an embedded video.
	 * @return 	string
	 */
	public function embed_video($text)
	{
		// $text will be an array of matches when called from preg_replace_callback
		if (is_array($text))
		{
			$text = $text[0];
		}

		// Check for a scheme at the start of the URL, add it if necessary.
		$url = (substr($text, 0, 4) != 'http')? 'http://'.$text : $text;
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
			// Add the video ID to the embed code.
			$text = str_replace(':video_id', $video_id, $embed_html);

			// Add the iframe width and height to the embed code.
			$text = str_replace(':width', $this->_embed_width, $text);
			$text = str_replace(':height', $this->_embed_height, $text);
		}

		// Nothing was matched, return the text unaltered.
		return $text;
	}

	/**
	 * Set the iframe width for embedded flash videos.
	 *
	 * @param integer
	 * @return Chunk_Text
	 */
	public function embed_width($width)
	{
		// Set the new width.
		$this->_embed_width = $width;

		// Return the current text chunk.
		return $this;
	}

	public function has_content()
	{
		return trim($this->_chunk->text) != NULL;
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
		return preg_replace('|<(.*?)src=([\'"])/asset/view/(\d+)([\'"])(.*?)>|', '<$1src=$2hoopdb://image/$3$4$5>', $text);
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
					->url();
			},
			$text
		);

		return $text;
	}
}