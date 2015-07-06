<?php

namespace BoomCMS\Core\Commands;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class CreatePage extends Command implements SelfHandling
{

    protected $auth;
    protected $parent;
    protected $provider;

    public function __construct(Page\Provider $provider, Auth $auth, Page\Page $parent = null)
    {
        $this->auth = $auth;
        $this->parent = $parent;
        $this->provider = $provider;
    }

    public function handle()
    {
        $attrs = [
            'visible_from' => time(),
            'created_by' => $this->auth->getPerson()->getId()
        ];

        if ($this->parent) {
            $attrs  = [
                'visible_from' => time(),
                'parent_id' => $this->parent->getId(),
                'visible_in_nav' => $this->parent->childrenAreVisibleInNav(),
                'visible_in_nav_cms' => $this->parent->childrenAreVisibleInCmsNav(),
                'children_visible_in_nav' => $this->parent->childrenAreVisibleInNav(),
                'children_visible_in_nav_cms' => $this->parent->childrenAreVisibleInCmsNav(),
            ];
        }

        $page = $this->provider->create($attrs);

        $page->addVersion([
            'edited_by' => $this->auth->getPerson()->getId(),
            'page_id' => $page->getId(),
            'template_id'  => $this->parent? $this->parent->getDefaultChildTemplateId() : null,
            'title' => 'Untitled',
            'published' => true,
            'embargoed_until' => time(),
        ]);

        return $page;
    }
}