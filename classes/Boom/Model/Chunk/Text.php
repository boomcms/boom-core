<?php

namespace Boom\Model\Chunk;

use Boom\TextFilter\Commander as TextFilter;
use Boom\TextFilter\Filter as Filter;
use \Database_Exception as Database_Exception;
use \DB as DB;
use \Kohana_Exception as Kohana_Exception;

class Text extends \ORM
{
	protected $_table_columns = array(
		'text'		=>	'',
		'id'		=>	'',
		'slotname'	=>	'',
		'page_vid' => '',
		'is_block'	=>	'',
		'site_text' => '',
	);

	protected $_table_name = 'chunk_texts';

	public function _cleanText()
	{
		$Commander = new TextFilter;

		if ($this->slotname === 'standfirst') {
			$Commander->addFilter(new Filter\RemoveAllHTML);
		} else if ($this->is_block) {
			$Commander
				->addFilter(new Filter\MakeInternalLinksRelative)
				->addFilter(new Filter\PurifyHTML)
				->addFilter(new Filter\MungeAssetEmbeds)
				->addFilter(new Filter\MungeRelativeInternalLinks);
		} else {
			$Commander->addFilter(new Filter\RemoveHTMLExceptInlineElements);
		}

		$this->text = $Commander->filterText($this->text);
	}

	/**
	 *
	 * @param	Validation $validation
	 * @return 	Boom_Model_Chunk_Text
	 */
	public function create(Validation $validation = null)
	{
		$this->_cleanText();

		// Find which assets are linked to within the text chunk.
		preg_match_all('~hoopdb://((image)|(asset))/(\d+)~', $this->_object['text'], $matches);

		$commander = new TextFilter;
		$commander
			->addFilter(new Filter\OEmbed)
			->addFilter(new Filter\StorifyEmbed)
			->addFilter(new Filter\UnmungeAssetEmbeds)
			->addFilter(new Filter\RemoveLinksToInvisiblePages)
			->addFilter(new Filter\UnmungeInternalLinks);

		$this->site_text = $commander->filterText($this->_object['text']);

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
}