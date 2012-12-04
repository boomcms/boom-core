<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package Sledge
 * @category Helpers
*/
class Sledge_Text extends Kohana_Text
{
	/**
	 * Auto-embed youtube and similar videos.
	 * Turns a link to a youtube (vimeo, etc.) page into an embedded video.
	 *
	 * This could be split out in object orientated awesomeness, but for now this will do as we don't need a whole lot of flexibility
	 *
	 * @param	string	$html
	 * @return 	$html
	 */
	public static function auto_link_video($text)
	{
		// This mammoth regular expression matches a URL
		//  Rob didn't write this, I'm not smart enough for this.
		// See http://daringfireball.net/2010/07/improved_regex_for_matching_urls for an explanation if curious.
		return preg_replace_callback('~(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))~', function($matches){
			$url = parse_url($matches[0]);

			if (strpos($url['host'], 'youtube') !== FALSE)
			{
				// Youtube video
				parse_str($url['query']);

				if (isset($v))
				{
					return "<iframe width='560' height='315' src='http://www.youtube.com/embed/$v' frameborder='0' allowfullscreen></iframe>";
				}
			}
			elseif (strpos($url['host'], 'vimeo') !== FALSE)
			{
				// Vimeo video
				$id = str_replace("/", "", $url['path']);

					return "<iframe src='http://player.vimeo.com/video/$id' width='500' height='281' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
			}

			return $matches[0];
		}, $text);
	}

	/**
	 * Turns text chunk contents into HTML.
	 * e.g. replaces hoopdb:// links to <img> and <a> links
	 *
	 * @param	string	$text	Text to decode
	 * @return 	string
	 */
	public static function decode_chunk($text)
	{
		// Image links in the form hoopdb://image/123
		$text = preg_replace('|hoopdb://image/(\d+)|', Sledge_Asset::PATH . '$1/400', $text);

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

	/**
	 * Encodes text chunk contents to be saved in the database.
	 * e.g. Turns text links, such as <img src='/asset/view/324'> in hoopdb:// links
	 *
	 * @param 	string	$text	Text to encode
	 * @return 	string
	 */
	public static function encode_chunk($text)
	{
		$text = preg_replace('|<(.*?)src=[\'"]' . Sledge_Asset::PATH . '(\d+)(.*?)[\'"](.*)>|', '<$1 src="hoopdb://image/$2"$4>', $text);
		return $text;
	}
}