<?php

namespace BoomCMS\Http\Controllers\CMS\People\Person;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Person;
use BoomCMS\Core\Group;
use BoomCMS\Http\Controllers\CMS\People\PeopleManager;
use Illuminate\Http\Request;

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
    public $editPerson;

    protected $personProvider;
    protected $groupProvider;

    public function __construct(Auth $auth,
        Person\Provider $personProvider,
        Group\Provider $groupProvider,
        Request $request
    ) {
        $this->auth = $auth;
        $this->personProvider = $personProvider;
        $this->groupProvider = $groupProvider;
        $this->request = $request;

        $this->editPerson = $this->personProvider->findById($this->request->route()->getParameter('id'));
    }
}
