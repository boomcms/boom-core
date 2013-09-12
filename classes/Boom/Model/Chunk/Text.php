<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 * @author	Rob Taylor
 *
 */
class Boom_Model_Chunk_Text extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_columns = array(
		'text'		=>	'',
		'id'		=>	'',
		'title'		=>	'',
		'slotname'	=>	'',
	);

	protected $_table_name = 'chunk_texts';

	/**
	 * Clean the text with HTML Purifier.
	 *
	 * @param string $text
	 * @return string
	 */
	public function clean_text($text)
	{
		if ($this->slotname == 'standfirst')
		{
			// For standfirsts remove all HTML tags.
			return strip_tags($text);
		}
		elseif (substr($this->slotname, 0, 8) == 'bodycopy')
		{
			// For the bodycopy clean the HTML.
			require_once Kohana::find_file('vendor', 'htmlpurifier/library/HTMLPurifier.auto');

			// Get the HTML Purifier config from a config file.
			$config = HTMLPurifier_Config::createDefault();
			$config->loadArray(Kohana::$config->load('htmlpurifier'));

			// Create a purifier object.
			$purifier = new HTMLPurifier($config);

			// Return the cleaned text.
			return $purifier->purify($text);
		}
		else
		{
			// For everything else allow b, i , and a tags.
			return strip_tags($text, '<b><i><a>');
		}
	}

	/**
	 * When creating a text chunk log which assets are linked to from it.
	 *
	 * @param	Validation $validation
	 * @return 	Boom_Model_Chunk_Text
	 */
	public function create(Validation $validation = NULL)
	{
		// Clean the text.
		// This is done now rather than as a filter as the rules for what is allowed in the text varies with the slotname.
		// Using a filter we can't be sure that the slotname has been set before the text which could result in the wrong rules being applied.
		$this->_object['text'] = $this->clean_text($this->_object['text']);

		// Munge links in the text, e.g. to assets.
		 // This needs to be done after the text is cleaned by HTML Purifier because HTML purifier strips out invalid images.
		$this->_object['text'] = $this->munge($this->_object['text']);

		// Find which assets are linked to within the text chunk.
		preg_match_all('~hoopdb://((image)|(asset))/(\d+)~', $this->_object['text'], $matches);

		// Create the text chunk.
		parent::create($validation);

		// Are there any asset links?
		if ( ! empty($matches[4]))
		{
			$assets = array_unique($matches[4]);

			// Log which assets are being referenced with a multi-value insert.
			$query = DB::insert('chunk_text_assets', array('chunk_id', 'asset_id', 'position'));

			foreach ($assets as $i => $asset_id)
			{
				$query->values(array($this->id, $asset_id, $i));
			}

			try
			{
				$query->execute();
			}
			catch (Database_Exception $e)
			{
				// Don't let database failures in logging prevent the chunk from being saved.
				Kohana_Exception::log($e);
			}
		}

		return $this;
	}

	public function filters()
	{
		return array(
			'text' => array(
				array(
					function($text)
					{
						return str_replace('&nbsp;', ' ', $text);
					}
				),
			),
			'title'	=> array(
				array('strip_tags'),
				array(
					function($text)
					{
						return str_replace('&nbsp;', ' ', $text);
					}
				),
			)
		);
	}

	/**
	 * Munges text chunk contents to be saved in the database.
	 * e.g. Turns text links, such as <img src='/asset/view/324'> in hoopdb:// links
	 *
	 * @param 	string	$text		Text to munge
	 * @return 	string
	 *
	 */
	public function munge($text)
	{
		$text = preg_replace('|<(.*?)src=([\'"])/asset/view/(.*?)([\'"])(.*?)>|', '<$1src=$2hoopdb://image/$3$4$5>', $text);
		$text = preg_replace('|<(.*?)href=([\'"])/asset/view/(\d+)/?.*?([\'"])(.*?)>|', '<$1href=$2hoopdb://asset/$3$4$5>', $text);

		return $text;
	}

	/**
	 * Turns text chunk contents into HTML.
	 * e.g. replaces hoopdb:// links to <img> and <a> links
	 *
	 * @param string
	 * @return string
	 */
	public function unmunge($text)
	{
		$text = $this->unmunge_image_links_with_only_asset_id($text);
		$text = $this->unmunge_image_links_with_multiple_params($text);
		$text = $this->unmunge_non_image_asset_links($text);
		$text = $this->unmunge_page_links($text);

		return $text;
	}

	public function unmunge_image_links_with_only_asset_id($text)
	{
		return preg_replace('|hoopdb://image/(\d+)([\'"])|', '/asset/view/$1/400$2', $text);
	}

	public function unmunge_image_links_with_multiple_params($text)
	{
		return preg_replace('|hoopdb://image/(\d+)/|', '/asset/view/$1/', $text);
	}

	public function unmunge_non_image_asset_links($text)
	{
		return preg_replace_callback('|<a.*?href=[\'\"]hoopdb://asset/(\d+).*?</a>|', function($matches)
			{
				$asset_id = $matches[1];
				$asset = new Model_Asset($asset_id);

				if ($asset->loaded())
				{
					$text = "<p class='inline-asset'><a class='download ".Boom_Asset::type($asset->type)."' href='/asset/view/{$asset->id}'>Download {$asset->title}</a>";

					if (Editor::instance()->state_is(Editor::DISABLED))
					{
						$text .= " (".Text::bytes($asset->filesize)." ".ucfirst(Boom_Asset::type($asset->type)).")";
					}

					$text .= "</p>";
					return $text;
				}
			}, $text);
	}

	public function unmunge_page_links($text)
	{
		$text = preg_replace_callback('|hoopdb://page/(\d+)|',
			function ($match)
			{
				return new Model_Page_URL(array('page_id' => $match[1], 'is_primary' => TRUE));
			},
			$text
		);

		return $text;
	}
}