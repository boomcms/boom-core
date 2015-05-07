<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Person\Finder as PersonFinder;
use BoomCMS\Core\Group\Finder as GroupFinder;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Person;
use BoomCMS\Core\Controller\Controller;

use Illuminate\Http\Request;

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

    public function index(Person\Finder $finder)
    {
        $finder
            ->addFilter(new PersonFinder\Filter\GroupId($this->request->query('group')))
            ->setOrderBy('name', 'asc');

        return View::make($this->viewPrefix . 'list', [
            'people' => $finder->findAll()
        ]);
    }

    protected function _show(View $view = null)
    {
        if ( ! $this->request->is_ajax()) {
            $finder = new GroupFinder();

            return View::make("boompeople/manager", [
                'groups' => $finder->findAll(),
                'content' => $view,
            ]);
        }
    }

    public function after()
    {
        $this->_show($this->template);

        parent::after();
    }
}
