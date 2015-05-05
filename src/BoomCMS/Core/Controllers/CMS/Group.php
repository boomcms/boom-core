<?php

namespace BoomCMS\Core\Controllers\CMS;

use \Boom\Group as Group;

class Group extends PeopleManager
{
    /**
	 * @var string
	 */
    protected $viewDirectory = 'boom/groups';

    /**
	 * @var Model_Group
	 */
    public $group;

    public function before()
    {
        parent::before();

        $this->authorization('manage_people');
        $this->group = Group\Factory::byId($this->request->param('id'));
    }
}
