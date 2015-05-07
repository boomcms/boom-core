<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Group;

class Group extends PeopleManager
{
    protected $viewPrefix = 'boom::groups.';

    /**
     *
     * @var Group\Group
     */
    public $group;

    public function __construct(Group\Povider $provider)
    {
        $this->authorization('manage_people');

        $this->group = $this->provider->findById($this->request->param('id'));
    }
}
