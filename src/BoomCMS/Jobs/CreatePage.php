<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Contracts\Models\Site;
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
     * The site to which to add the new page.
     *
     * @var Site
     */
    protected $site;

    /**
     * @param Person $createdBy
     * @param Page   $parent
     */
    public function __construct(Person $createdBy, Site $site, Page $parent = null)
    {
        $this->createdBy = $createdBy;
        $this->parent = $parent;
        $this->site = $site;
    }

    public function handle()
    {
        $attrs = [
            'visible_from' => time(),
            'created_by'   => $this->createdBy->getId(),
            'site_id'      => $this->site->getId(),
        ];

        if ($this->parent) {
            $attrs = [
                'visible_from'                => time(),
                'parent_id'                   => $this->parent->getId(),
                'visible_in_nav'              => $this->parent->childrenAreVisibleInNav(),
                'visible_in_nav_cms'          => $this->parent->childrenAreVisibleInCmsNav(),
                'children_visible_in_nav'     => $this->parent->childrenAreVisibleInNav(),
                'children_visible_in_nav_cms' => $this->parent->childrenAreVisibleInCmsNav(),
            ];
        }

        $page = PageFacade::create($attrs);

        $page->addVersion([
            'template_id'     => $this->parent ? $this->parent->getDefaultChildTemplateId() : null,
            'title'           => 'Untitled',
            'embargoed_until' => time(),
        ]);

        return $page;
    }
}
