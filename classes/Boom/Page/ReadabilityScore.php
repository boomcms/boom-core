<?php

namespace Boom\Page;

use Boom\Page\Page as Page;

class ReadabilityScore
{
	/**
	 *
	 * @var \Model_Page
	 */
	protected $_page;

	public function __construct(Page $page)
	{
		$this->_page = $page;
		$this->_loadDependencies();
	}

	protected function _getPageText()
	{
		$chunks = \ORM::factory('Chunk_Text')
			->where('is_block', '=', true)
			->where('page_vid', '=', $this->_page->getCurrentVersion()->id)
			->find_all();

		$text = "";
		foreach ($chunks as $chunk)
		{
			$text .= $chunk->text;
		}

		return $text;
	}

	public function getSmogScore()
	{
		$text = $this->_getPageText();

		if (strlen($text) > 100)
		{
			$stats = new \TextStatistics;
			return $stats->smog_index($text);
		}
	}

	protected function _loadDependencies()
	{
		if ( ! class_exists('TextStatistics'))
		{
			require \Kohana::find_file('vendor/text-statistics', 'TextStatistics');
		}
	}
}