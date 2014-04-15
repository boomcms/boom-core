<?php

class Boom_Page_ReadabilityScore
{
	/**
	 *
	 * @var Model_Page
	 */
	protected $_page;

	public function __construct(Model_Page $page)
	{
		$this->_page = $page;

		$this->_load_dependencies();
	}

	protected function _get_page_text()
	{
		$chunks = ORM::factory('Chunk_Text')
			->where('is_block', '=', true)
			->where('page_vid', '=', $this->_page->version()->id)
			->find_all();

		$text = "";
		foreach ($chunks as $chunk)
		{
			$text .= $chunk->text;
		}

		return $text;
	}

	public function get_smog_score()
	{
		$text = $this->_get_page_text();

		if (strlen($text) > 100)
		{
			$stats = new TextStatistics;
			return $stats->smog_index($text);
		}
	}

	protected function _load_dependencies()
	{
		if ( ! class_exists('TextStatistics'))
		{
			require Kohana::find_file('vendor/text-statistics', 'TextStatistics');
		}
	}


}