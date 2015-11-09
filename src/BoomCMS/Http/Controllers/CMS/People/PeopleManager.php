<?php

namespace BoomCMS\Http\Controllers\CMS\People;

use BoomCMS\Support\Facades\Person;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class PeopleManager extends Controller
{
    protected $viewPrefix = 'boomcms::people.';

    protected $role = 'manage_people';

    public function index()
    {
        $groupId = $this->request->input('group');
        $people = $groupId ? Person::findByGroupId($groupId) : Person::findAll();

        return View::make($this->viewPrefix.'list', [
            'people' => $people,
        ]);
    }
}
