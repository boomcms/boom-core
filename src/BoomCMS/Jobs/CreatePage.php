<?php

namespace BoomCMS\Jobs;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\PageWasCreated;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class CreatePage extends Command
{
    /**
     * @var Page
     */
    protected $parent;

    /**
     * @var string
     */
    protected $title = Page::DEFAULT_TITLE;

    /**
     * @param Page $parent
     */
    public function __construct(Page $parent = null)
    {
        $this->parent = $parent;
    }

    public function handle()
    {
        $attrs = [
            'visible_from' => time(),
        ];

        if ($this->parent) {
            $attrs += [
                'parent_id'                   => $this->parent->getId(),
                'visible_in_nav'              => $this->parent->childrenAreVisibleInNav(),
                'visible_in_nav_cms'          => $this->parent->childrenAreVisibleInCmsNav(),
                'children_visible_in_nav'     => $this->parent->childrenAreVisibleInNav(),
                'children_visible_in_nav_cms' => $this->parent->childrenAreVisibleInCmsNav(),
                'enable_acl'                  => $this->parent->aclEnabled(),
            ];
        }

        $page = PageFacade::create($attrs);

        $page->addVersion([
            'template_id'     => $this->parent ? $this->parent->getDefaultChildTemplateId() : null,
            'title'           => $this->title,
            'embargoed_until' => time(),
        ]);

        if ($this->parent) {
            $groupIds = $this->parent->getAclGroupIds();

            foreach ($groupIds as $groupId) {
                $page->addAclGroupId($groupId);
            }
        }

        Event::fire(new PageWasCreated($page, $this->parent));

        return $page;
    }

    /**
     * Set a title to be used for the new page.
     *
     * @param type $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
