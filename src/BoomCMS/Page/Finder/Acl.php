<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Site;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Database\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Acl extends Filter
{
    /**
     * @var Person|null
     */
    protected $person;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @param Site $site
     * @param Person|null $person
     */
    public function __construct(Site $site, Person $person = null)
    {
        $this->site = $site;
        $this->person = $person;
    }

    public function build(Builder $query)
    {
        if ($this->person === null) {
            return $query->where(Page::ATTR_ENABLE_ACL, false);
        }

        return $query
            ->leftJoin('page_acl')
            ->on('pages.id', '=', 'page_acl.page_id')
            ->leftJoin('group_person')
            ->on('page_acl.group_id', '=', 'group_person.group_id')
            ->where(function(Builder $where) {
                $where
                    ->where(Page::ATTR_CREATED_BY, $this->person->getId())
                    ->orWhereNull('page_acl.group_id')
                    ->orWhere('group_person.person_id', $this->person->getId());
            });
    }

    public function shouldBeApplied()
    {
        return $this->person === null || $this->person->can('managePages', $this->site) !== true;
    }
}
