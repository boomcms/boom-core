<?php

namespace BoomCMS\Jobs;

use BoomCMS\Core\Page\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\DB;

class ReorderChildPages extends Command implements SelfHandling
{
    /**
     * @var Page
     */
    protected $parent;

    /**
     * @var array
     */
    protected $sequences;

    /**
     * @param Page $page
     * @param array $sequences
     */
    public function __construct(Page $page, array $sequences)
    {
        $this->parent = $page;
        $this->sequences = $sequences;
    }

    public function handle()
    {
        DB::transaction(function () {
            foreach ($this->sequences as $sequence => $pageId) {
                $page = PageFacade::findById($pageId);

                // Only update the sequence of pages which are children of this page.
                if ($this->parent->isParentOf($page)) {
                    $page->setSequence($sequence);

                    PageFacade::save($page);
                }
            }
        });
    }
}
