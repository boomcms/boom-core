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
	* Filters for the versioned person columns
	* @link http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'text' => array(
				array('html_entity_decode'),
				array('urldecode'),
				array('Chunk_Text::munge'),
			),
	   );
	}

	/**
	 * When creating a text chunk log which assets are linked to from it.
	 *
	 * @param	Validation $validation
	 * @return 	Boom_Model_Chunk_Text
	 */
	public function save(Validation $validation = NULL)
	{
		// Create the text chunk.
		parent::save($validation);

		// Find which assets are linked to within the text chunk.
		preg_match_all('|hoopdb://image/(\d+)|', $this->text, $matches);

		// Are there any asset links?
		if ( ! empty($matches[1]))
		{
			$assets = array_unique($matches[1]);

			// Log which assets are being referenced with a multi-value insert.
			$query = DB::insert('chunk_text_assets', array('chunk_id', 'asset_id', 'position'));

			foreach ($assets as $i => $asset_id)
			{
				$query->values(array($this->chunk_id, $asset_id, $i));
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
}