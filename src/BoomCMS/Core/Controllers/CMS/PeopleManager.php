<?php

namespace BoomCMS\Core\Controllers\CMS;

use \Boom\Person\Finder as PersonFinder;
use \Boom\Group\Finder as GroupFinder;
use BoomCMS\Core\Controller\Controller;

class PeopleManager extends Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_people');
    }

    public function index()
    {
        $finder = new PersonFinder();
        $finder
            ->addFilter(new PersonFinder\Filter\GroupId($this->request->query('group')))
            ->setOrderBy('name', 'asc');

        $this->template = new View("boom/people/list", [
            'people' => $finder->findAll()
        ]);
    }

    protected function _show(View $view = null)
    {
        if ( ! $this->request->is_ajax()) {
            $finder = new GroupFinder();

            $this->template = View::factory("boom/people/manager", [
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
