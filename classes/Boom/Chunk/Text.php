<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package	BoomCMS
 * @category	Chunks
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
	public static $youtube_embed = '<iframe width=":width" height=":height" src="http://www.youtube.com/embed/:video_id" frameborder="0" allowfullscreen></iframe>';

	/**
	 * Embed HTML for a Vimeo video.
	 *
	 * @var string
	 */
	public static $vimeo_embed = '<iframe width=":width" height=":height" src="http://player.vimeo.com/video/:video_id" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

	protected function _add_html($text)
	{
		$title = " title='".$this->_chunk->title."'";

		switch ($this->_chunk->slotname)
		{
			case 'standfirst':
				return "<p class=\"standFirst\"$title>$text</p>";
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
	 * @uses Model_Chunk_Text::unmunge()
	 * @uses Chunk_Text::embed_video()
	 */
	protected function _show()
	{
		$text = $this->text();

		// If no template has been set then add the default HTML tags for this slotname.
		if ($this->_template === NULL)
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

		if ( ! $text)
		{
			$text = Kohana::message('chunks', 'text');
		}

		$template = ($this->_template === NULL)? $this->_slotname : $this->_template;

		if ( ! Kohana::find_file('views', $this->_view_directory."text/$template"))
		{
			return "<p>$text</p>";
		}
		else
		{
			return View::factory($this->_view_directory."text/$template", array(
				'text'	=>	$text,
				'title'	=>	$this->_chunk->title,
				'chunk' => $this->_chunk,
			));
		}
	}

	/**
	 *
	 * @return Chunk_Text
	 */
	public function embed_height($height)
	{
		$this->_embed_height = $height;

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
	 * @return Chunk_Text
	 */
	public function embed_width($width)
	{
		$this->_embed_width = $width;

		return $this;
	}

	public function has_content()
	{
		return trim($this->_chunk->text) != NULL;
	}

	public function text()
	{
		$text = $this->_chunk->text;

		$text = html_entity_decode($this->_chunk->text);
		$text = $this->_chunk->unmunge($text);

		// When in site view...
		if (Editor::instance()->state() != Editor::EDIT)
		{
			// Embed youtube videos.
			$text = preg_replace_callback('~(?<!href=[\'\"])(?:ht|f)tps?://[^<\s]+(?:/|\b)~i', array($this, 'embed_video'), $text);

			// If we're displaying a bodycopy link to the images in the text.
			// We can then use these links to show the images in an larger popup when the link is clicked.
			// Both the regular expressions below match images which aren't already part of a link.
			// The first regex matches images from the asset manager and links to the full size image (i.e. excludes width and height from the link).
			// The second regex matches all other images and links to the image as it appears in the img tag.
			$text = preg_replace('~(?<!href="|">)<img.*?src=["\']/asset/view/(\d+).*?["\'].*?>(?!\<\/a\>)~i', '<a href="/asset/view/${1}" class="b-image">${0}</a>', $text);
			$text = preg_replace('~(?<!href="|">)<img.*?src=["\'](.*?)["\'].*?>(?!\<\/a\>)~i', '<a href="${1}" class="b-image">${0}</a>', $text);
		}

		return $text;
	}
}