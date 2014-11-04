<?php

namespace Boom\Page;

class ReadabilityScore
{
    /**
	 *
	 * @var Page
	 */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    protected function _getPageText()
    {
        $chunks = \ORM::factory('Chunk_Text')
            ->where('is_block', '=', true)
            ->where('page_vid', '=', $this->page->getCurrentVersion()->id)
            ->find_all();

        $text = "";
        foreach ($chunks as $chunk) {
            $text .= $chunk->text;
        }

        return $text;
    }

    public function getSmogScore()
    {
        if (class_exists('TextStatistics')) {
            $text = $this->_getPageText();

            if (strlen($text) > 100) {
                $stats = new TextStatistics();

                return $stats->smog_index($text);
            }
        }

        return 0;
    }
}
