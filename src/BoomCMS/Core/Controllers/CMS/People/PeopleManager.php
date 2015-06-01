<?php

namespace BoomCMS\Core\Controllers\CMS\People;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Person;

use BoomCMS\Core\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PeopleManager extends Controller
{
    protected $viewPrefix = 'boom::people.';

    public function __construct(Request $request, Auth\Auth $auth, Person\Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->provider = $provider;

        $this->authorization('manage_people');
    }

    public function index()
    {
        $groupId = $this->request->input('group');
        $people = $groupId ? $this->provider->findByGroupId($groupId) : $this->provider->findAll();

        return View::make($this->viewPrefix . 'list', [
            'people' => $people
        ]);
    }
}
