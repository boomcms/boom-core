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

    /**
     * @param Page $page
     * @param array $options
     */
    public function __construct(Page $page, array $options = [])
    {
        $this->page = $page;
        $this->options = $options;
    }

    public function handle()
    {
        $this->childrenShouldBeMoved() ?
            $this->keepChildren()
            : $this->deleteAll();
    }

    /**
     * @return bool
     */
    protected function childrenShouldBeMoved()
    {
        return isset($this->options['reparentChildrenTo'])
            && $this->options['reparentChildrenTo'] > 0;
    }

    /**
     * @param Page $page
     */
    protected function delete(Page $page)
    {
        PageFacade::delete($page);
        Event::fire(new PageWasDeleted($page));
    }

    protected function deleteAll()
    {
        PageFacade::recurse($this->page, function(Page $page) {
            $this->delete($page);
        });
    }

    protected function keepChildren()
    {
        $this->reparentChildren();

        if ($this->urlsShouldBeReassigned()) {
            $this->reassignURLs();
        }

        $this->delete($this->page);
    }

    protected function reparentChildren()
    {
        $children = PageFacade::findByParentId($this->page->getId());
        $newParent = PageFacade::find($this->options['reparentChildrenTo']);

        foreach ($children as $child) {
            $child->setParent($newParent);
            PageFacade::save($child);
        }
    }

    protected function reassignURLs()
    {
        $redirectTo = PageFacade::find($this->options['redirectTo']);

        if ($redirectTo !== null) {
            foreach ($this->page->getUrls() as $url) {
                Bus::dispatch(new ReassignURL($url, $redirectTo));
            }
        }
    }

    /**
     * 
     * @return bool
     */
    protected function urlsShouldBeReassigned()
    {
        return isset($this->options['redirectTo']) && $this->options['redirectTo'] > 0;
    }
}
