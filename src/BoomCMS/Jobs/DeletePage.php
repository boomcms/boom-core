<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Events\PageWasDeleted;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

class DeletePage extends Command
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var array
     */
    protected $options;

    public function __construct(Page $page, array $options = [])
    {
        $this->page = $page;
        $this->options = $options;
    }

    public function handle()
    {
        $this->deleteChildren();
        $this->reassignURLs();

        PageFacade::delete($this->page);
        Event::fire(new PageWasDeleted($this->page));
    }

    /**
     * @return bool
     */
    protected function childrenShouldBeMoved()
    {
        return isset($this->options['reparentChildrenTo'])
            && $this->options['reparentChildrenTo'] > 0;
    }

    protected function deleteChildren()
    {
        if (!$this->childrenShouldBeMoved()) {
            Bus::dispatch(new DeletePageChildren($this->page));
        } else {
            $children = PageFacade::findByParentId($this->page->getId());
            $newParent = PageFacade::find($this->options['reparentChildrenTo']);

            foreach ($children as $child) {
                $child->setParent($newParent);
                PageFacade::save($child);
            }
        }
    }

    protected function reassignURLs()
    {
        if (isset($this->options['redirectTo']) && $this->options['redirectTo'] > 0) {
            $redirectTo = PageFacade::find($this->options['redirectTo']);

            if ($redirectTo) {
                foreach ($this->page->getUrls() as $url) {
                    Bus::dispatch(new ReassignURL($url, $redirectTo));
                }
            }
        }
    }
}
