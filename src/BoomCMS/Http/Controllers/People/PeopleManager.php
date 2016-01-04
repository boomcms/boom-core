<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;

class PeopleManager extends Controller
{
    protected $viewPrefix = 'boomcms::people.';

    protected $role = 'managePeople';

    public function index()
    {
        $groupId = $this->request->input('group');
        $people = $groupId ? Person::findByGroupId($groupId) : Person::findAll();

        return view($this->viewPrefix.'list', [
            'people' => $people,
        ]);
    }
}
