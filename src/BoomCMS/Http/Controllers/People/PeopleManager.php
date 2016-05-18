<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Http\Controllers\Controller;

class PeopleManager extends Controller
{
    protected $viewPrefix = 'boomcms::people-manager.';
    protected $role = 'managePeople';

    public function index()
    {
        return view($this->viewPrefix.'index');
    }
}
