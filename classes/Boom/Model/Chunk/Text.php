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
		elseif ($this->slotname == 'bodycopy')
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
		// Find which assets are linked to within the text chunk.
		preg_match_all('|hoopdb://image/(\d+)|', $this->text, $matches);

		// Clean the text.
		// This is done now rather than as a filter as the rules for what is allowed in the text varies with the slotname.
		// Using a filter we can't be sure that the slotname has been set before the text which could result in the wrong rules being applied.
		$this->_object['text'] = $this->clean_text($this->_object['text']);

		// Create the text chunk.
		parent::create($validation);

		// Are there any asset links?
		if ( ! empty($matches[1]))
		{
			$assets = array_unique($matches[1]);

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

	/**
	 * @link http://kohanaframework.org/3.2/guide/orm/filters
	 */
	public function filters()
	{
		return array(
			'text' => array(
				array('urldecode'),
				array('html_entity_decode'),
				array('Chunk_Text::munge'),
			),
			'title'	=> array(
				array('strip_tags'),
			)
		);
	}
}
