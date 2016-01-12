<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class CreatePage extends Command implements SelfHandling
{
    /**
     * @var Person
     */
    protected $createdBy;

    /**
     * @var Page
     */
    protected $parent;

    /**
     * @param Person $createdBy
     * @param Page   $parent
     */
    public function __construct(Person $createdBy, Page $parent = null)
    {
        $this->createdBy = $createdBy;
        $this->parent = $parent;
    }

    public function handle()
    {
        $attrs = [
            'visible_from' => time(),
            'created_by'   => $this->createdBy->getId(),
        ];

        if ($this->parent) {
            $attrs = [
                'visible_from'                => time(),
                'parent_id'                   => $this->parent->getId(),
                'visible_in_nav'              => $this->parent->childrenAreVisibleInNav(),
                'visible_in_nav_cms'          => $this->parent->childrenAreVisibleInCmsNav(),
                'children_visible_in_nav'     => $this->parent->childrenAreVisibleInNav(),
                'children_visible_in_nav_cms' => $this->parent->childrenAreVisibleInCmsNav(),
                'site_id'                     => $site->getId(),
            ];
        }

        $page = PageFacade::create($attrs);

        $page->addVersion([
            'edited_by'       => $this->createdBy->getId(),
            'page_id'         => $page->getId(),
            'template_id'     => $this->parent ? $this->parent->getDefaultChildTemplateId() : null,
            'title'           => 'Untitled',
            'published'       => true,
            'embargoed_until' => time(),
        ]);

        return $page;
    }
}
