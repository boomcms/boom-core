<?php

namespace BoomCMS\Http\Controllers\People\Person;

use BoomCMS\Http\Controllers\People\PeopleManager;
use Illuminate\Http\Request;

class BasePerson extends PeopleManager
{
    /**
     * @var string Directory where the views which relate to this class are held.
     */
    protected $viewPrefix = 'boomcms::person.';

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

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->authorize('managePeople', $request);
        $this->editPerson = $request->route()->getParameter('person');
    }
}
