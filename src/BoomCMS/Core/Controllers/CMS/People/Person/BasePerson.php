<?php

namespace BoomCMS\Core\Controllers\CMS\People\Person;

use BoomCMS\Core\Controllers\CMS\People\PeopleManager;

class BasePerson extends PeopleManager
{
    /**
	 * @var string Directory where the views which relate to this class are held.
	 */
    protected $viewPrefix = 'boom::person.';

    /**
	 * Person object to be edited.
	 *
	 * **CAUTION**
	 *
	 * [Boom_Controller::before()] sets a person property which is the logged in person.
	 * YOU DON'T WANT TO USE THE WRONG PROPERTY.
	 *
	 * @var Model_Person
	 */
    public $edit_person;

    public function before()
    {
        parent::before();

        $this->edit_person = Person\Factory::byId($this->request->param('id'));
    }
}
